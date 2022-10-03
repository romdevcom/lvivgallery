<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhotoRequest;
use App\Http\Requests\InterviewRequest;
use App\Repositories\CollectionRepository;
use App\Repositories\EntityRepository;
use App\Repositories\ObjectRepository;
use App\Repositories\PlaceRepository;
use App\Searcher;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Http\DeepzoomFactory;

class InterviewController extends ObjectController
{
	public function index(InterviewRequest $request){

		$user = isset($_COOKIE['user-id']) ? $_COOKIE['user-id'] : false;
		$user = DB::connection('mysql_users')->table('users')->where('id', $user)->get()->toArray();
		$user = !empty($user) && is_array($user) ? $user[0] : $user;
		$allow = $this->isUserAllow($user);
		$list = true;

		$all = $request->all();
		$query_params = $all;

		$params = getAllFilters($all);
		$searcher = new Searcher();
		$string = $request->only('full-search');
		$string = !empty($string) ? $string['full-search'] : false;

		$interviewIds = $searcher->browseObjectSearch($params['params']['interview'],$string);
		$interviewCount = count($interviewIds);
		$all['lang'] = lang_code();
		$queryString = sanitizeQueryString($request->getQueryWithoutPage());
		$currentPage = $request->currentPage();
		$pages = ceil((count($interviewIds) / 6));
		$pages ?: $pages = 1;
		$start = $currentPage * 6 - 6;

		if(!empty($interviewIds)) {
			$pageIds = array_slice($interviewIds, $start, 6);
			$interviewRepo = new ObjectRepository($pageIds, 247);
			$results = $interviewRepo->softLoad();
			$attributesBaseDate = $interviewRepo->getAttributes(true, 15, [96]);
			$attributesDate = array();
			foreach ($attributesBaseDate as $item) {
				$attributesDate[$item->row_id] = $item->value_longtext1;
			};
			$attributesBaseDescription = $interviewRepo->getAttributes(true, $all['lang'], [38]);
			$attributesDescription = array();
			foreach ($attributesBaseDescription as $item) {
				$attributesDescription[$item->row_id] = $item->value_longtext1;
			};
			foreach ($results as $result) {
				$result->date = isset($attributesDate[$result->id]) ? $this->interview_date($attributesDate[$result->id]) : false;
				$result->description = isset($attributesDescription[$result->id]) ? $attributesDescription[$result->id] : false;
			}
		}else{
			$results = false;
		}

		$photoFilters = $params['filters']['photo'];
		$videoFilters = $params['filters']['video'];
        $mapFilters = $params['filters']['map'];
		$collectionsFilters = $params['filters']['collection'];
        $photoFilters = !empty($photoFilters) ? rtrim($photoFilters,'&') : '';
        $mapFilters = !empty($mapFilters) ? rtrim($mapFilters,'&') : '';
        $videoFilters = !empty($videoFilters) ? rtrim($videoFilters,'&') : '';
        $collectionsFilters = !empty($collectionsFilters) ? rtrim($collectionsFilters,'&') : '';

		$videoIds =  $searcher->browseObjectSearch($params['params']['video'],$string);
		$videoCount = count($videoIds);
		$photoIds =  $searcher->browseObjectSearch($params['params']['photo'],$string);
		$photoCount = count($photoIds);
        $mapIds =  $searcher->browseObjectSearch($params['params']['map'],$string);
        $mapCount = count($mapIds);

		$collectionPhotoIDs = DB::table('ca_objects_x_collections')->whereIn('object_id', $photoIds)->pluck('collection_id')->toArray();
		$collectionVideosIDs = DB::table('ca_objects_x_collections')->whereIn('object_id', $videoIds)->pluck('collection_id')->toArray();
        $collectionMapIDs = DB::table('ca_objects_x_collections')->whereIn('object_id', $mapIds)->pluck('collection_id')->toArray();
        $collectionIds = !empty($collectionPhotoIDs) ? array_unique($collectionPhotoIDs) : array(0);
		$collectionIds = !empty($collectionVideosIDs) ? array_merge(array_unique($collectionVideosIDs), $collectionIds) : $collectionIds;
        $collectionIds = !empty($collectionMapIDs) ? array_merge(array_unique($collectionMapIDs), $collectionIds) : $collectionIds;

        if(isset($all['collection_type']) && !empty($all['collection_type'])){
			$repoCollection = new CollectionRepository([]);
			$collectionIds = !empty($collectionType) && !is_array($collectionType) ? $repoCollection->get_collections_by_type($collectionType, $collectionIds, 'ids') : $collectionIds;
		}
        $collectionCount = count(array_unique($collectionIds));

		$slug = 'interviews';

        $interviewCollectionsIds = array();
		$interviewCollectionsRepo = DB::table('ca_collections as c')
			->join('ca_collection_labels as l', 'c.collection_id', 'l.collection_id')
			->where([['c.type_id',254],['l.locale_id',$all['lang']]])
			->get();
		if(!empty($interviewCollectionsRepo) && count($interviewCollectionsRepo) > 0){
			$interviewCollections = array();
			foreach ($interviewCollectionsRepo as $item){
				$countQuery =  DB::table('ca_objects as o')
					->join('ca_objects_x_collections as oc', 'o.object_id', 'oc.object_id')
					->join('ca_attributes as st_a', 'o.object_id', 'st_a.row_id')
					->join('ca_attribute_values as st_av', 'st_av.attribute_id', 'st_a.attribute_id')
					->where([['st_av.value_longtext1',250],['oc.collection_id', $item->collection_id]]);
				$count = $countQuery->count();
				if($count) {
				    array_push($interviewCollectionsIds, $item->collection_id);
					$interviewCollections[$item->collection_id] = array(
						'id' => $item->collection_id,
						'name' => $item->name,
						'count' => $count
					);
				}
			}
		}else{
			$interviewCollections = false;
		}

        $collectionObj = false;
        if(empty($query_params) && !empty($interviewCollectionsRepo) ){
            $list = false;
            $collectionObjRepo = new CollectionRepository($interviewCollectionsIds);
            $collectionObj = $collectionObjRepo->all()->toArray();
            foreach ($collectionObj as $item){
                $item->image = $collectionObjRepo->get_collection_image($item->id);
            }
        }

		$seo = array();
		$seo['title'] = trans('translations.interview').' | '.trans('translations.og_title');
		$seo['og_title'] = $seo['title'];
		$seo['description'] = trans('translations.interview').' | '.trans('translations.after_description');
		$seo['og_description'] = $seo['description'];

		return view('interview.index',
			compact('results', 'interviewCollections',
                'pages', 'queryString', 'currentPage',
                'photoCount', 'mapCount', 'videoCount','collectionCount', 'interviewCount',
                'slug',
                'photoFilters', 'mapFilters', 'videoFilters', 'collectionsFilters',
                'string', 'all', 'allow', 'seo', 'user',
                'list', 'collectionObj')
		);
	}

	public function show($id){
		$user = isset($_COOKIE['user-id']) ? $_COOKIE['user-id'] : false;
		$user = DB::connection('mysql_users')->table('users')->where('id', $user)->get()->toArray();
		$user = !empty($user) && is_array($user) ? $user[0] : $user;
		$allow = $this->isUserAllow($user);

		$all['lang'] = lang_code();
		$all['id'] = $id;
		$all['type'] = 'interview';

		$ids[] = $id;

		$photoRepo = new ObjectRepository($ids, 247);
		$results = $photoRepo->all();
		$first = $results->first();
		$first->pdf = $this->get_file($first->id, 'application/pdf');
		$first->audio = $this->get_file($first->id, 'audio/mpeg');
		$first->dates_from_to = !empty($first->dates_from_to) ? $this->interview_date($first->dates_from_to) : false;

		$collections = isset($first->collections) && count($first->collections) > 0 ? $first->collections : false;
		$collection = false;
		if($collections){
			foreach ($collections as $item){
				$collection = $item;
				break;
			}
		}
		$relatedId = DB::table('ca_objects as o')
			->join('ca_objects_x_collections as c', 'o.object_id', 'c.object_id')
			->join('ca_attributes as st_a', 'o.object_id', 'st_a.row_id')
			->join('ca_attribute_values as st_av', 'st_av.attribute_id', 'st_a.attribute_id')
			->where([['st_av.value_longtext1',250],['c.collection_id',$collection->id]])
			->inRandomOrder()
			->limit(2)
			->pluck('o.object_id')
			->toArray();
		$relatedRepo = new ObjectRepository($relatedId, 247);
		$related = $relatedRepo->softLoad();
		$attributesBaseDate = $relatedRepo->getAttributes(true,15,[96]);
		$attributesDate = array();
		foreach ($attributesBaseDate as $item){
			$attributesDate[$item->row_id] = $item->value_longtext1;
		};
		$attributesBaseDescription = $relatedRepo->getAttributes(true,$all['lang'],[38]);
		$attributesDescription = array();
		foreach ($attributesBaseDescription as $item){
			$attributesDescription[$item->row_id] = $item->value_longtext1;
		};
		foreach ($related as $item){
			$item->date = !empty($attributesDate[$item->id]) ? $this->interview_date($attributesDate[$item->id]) : false;
			$item->description = !empty($attributesDescription[$item->id]) ? $attributesDescription[$item->id] : false;
		}

		$seo = array();
		$seo['title'] = trans('translations.interview').': '.$first->name.' | '.trans('translations.og_title');
		$seo['og_title'] = $seo['title'];
		$seo['description'] = $first->name.' | '.trans('translations.after_description');
		$seo['og_description'] = $seo['description'];

		return view('interview.show', ['result' => $first, 'user' => $user, 'allow' => $allow, 'collection' => $collection, 'related' => $related, 'seo' => $seo]);
	}

	public function get_file($id, $type){
		$fileRepo = DB::table('ca_object_representations as r')
			->join('ca_objects_x_object_representations as x', 'r.representation_id', 'x.representation_id')
			->where([['x.object_id', $id],['r.mimetype', $type]])
			->get()->toArray();
		$fileRepo = !empty($fileRepo) && count($fileRepo) > 0 ? $fileRepo[0] : $fileRepo;
		if(function_exists('gzuncompress')){
			$ps_uncompressed_data = @gzuncompress($fileRepo->media);
			$media = (unserialize($ps_uncompressed_data));
		}else{
			$media = unserialize(base64_decode($fileRepo->media));
		}
		$file = media_url($media, 'original');
		return !empty($file) ? $file : false;
	}

	public function isUserAllow($user){
		$allow = false;
		if(!empty($user)) {
			if ($user->role == 1) {
				$allow = true;
			} else {
				$rights = DB::connection('mysql_users')->table('access')->where([['user_id', $user->id], ['status', 'on']])->get()->toArray();
				if (!empty($rights) && count($rights) > 0) {
					$date = date('Ymd');
					foreach ($rights as $right) {
						$allow = $date >= $right->date_from && $date < $right->date_to;
					}
				}
			}
		}
		return $allow;
	}

	public function interview_date($date){
		$date = explode('-',$date);
		$date = array_reverse($date);
		$date = join('.',$date);
		return $date;
	}
}