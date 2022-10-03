<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectionRequest;
use App\Http\Requests\InterviewRequest;
use App\Repositories\CollectionRepository;
use App\Repositories\ObjectRepository;
use App\Http\Requests\PhotoRequest;
use App\Http\Requests\VideoRequest;
use Illuminate\Http\Request;
use App\Searcher;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{
    public function index(CollectionRequest $request)
    {
		$string = $request->only('full-search');
		$string = !empty($string) ? $string['full-search'] : false;

		$all = $request->all();
		$collectionType = false;
		if($request->has('collection_type')) {
			$request->flash();
			$params = $request->only('collection_type');
		}

		//make filters (collections, date, place, full-search)
		$all = $request->all();
		$params = getAllFilters($all);
		$photoFilters = $params['filters']['photo'];
        $mapFilters = $params['filters']['map'];
		$videoFilters = $params['filters']['video'];
		$interviewFilters = $params['filters']['interview'];
        $photoFilters = !empty($photoFilters) ? rtrim($photoFilters,'&') : '';
        $mapFilters = !empty($mapFilters) ? rtrim($mapFilters,'&') : '';
        $videoFilters = !empty($videoFilters) ? rtrim($videoFilters,'&') : '';
        $interviewFilters = !empty($interviewFilters) ? rtrim($interviewFilters,'&') : '';
		$collectionsFilters = '?'.$request->getQueryWithoutPage();
		$collectionType = !empty($all['collection_type']) ? $all['collection_type'] : false;
		$collectionType = is_array($collectionType) ? array_shift($collectionType) : false;

		//photo and video count with filters
		$countSearcher = new Searcher();
		$videoIds = $countSearcher->browseObjectSearch($params['params']['video'], $string);
		$videoCount = count($videoIds);
		$photoIds =  $countSearcher->browseObjectSearch($params['params']['photo'],$string);
		$photoCount = count($photoIds);
		$interviewIds =  $countSearcher->browseObjectSearch($params['params']['interview'],$string);
		$interviewCount = count($interviewIds);
        $mapIds =  $countSearcher->browseObjectSearch($params['params']['map'],$string);
        $mapCount = count($mapIds);

		$repo = new CollectionRepository([]);
		$collectionIds = array();
		if(empty($all)) {
			$collectionIds = array_column($repo->get_collections($collectionType, $string), 'collection_id');
		}else{
			$collectionPhotoIDs = DB::table('ca_objects_x_collections')->whereIn('object_id', $photoIds)->pluck('collection_id')->toArray();
			$collectionVideosIDs = DB::table('ca_objects_x_collections')->whereIn('object_id', $videoIds)->pluck('collection_id')->toArray();
			$collectionMapsIDs = DB::table('ca_objects_x_collections')->whereIn('object_id', $mapIds)->pluck('collection_id')->toArray();

			$collectionIds = !empty($collectionPhotoIDs) ? array_merge(array_unique($collectionPhotoIDs), $collectionIds) : $collectionIds;
			$collectionIds = !empty($collectionVideosIDs) ? array_merge(array_unique($collectionVideosIDs), $collectionIds) : $collectionIds;
			$collectionIds = !empty($collectionMapsIDs) ? array_merge(array_unique($collectionMapsIDs), $collectionIds) : $collectionIds;
		}

		if(!empty($collectionIds)){

			$collectionTypeCount = array();
			$collectionTypeCount[261] = $repo->get_collections_by_type(261);
			$collectionTypeCount[260] = $repo->get_collections_by_type(260);

			$currentPage = $request->currentPage();
            $collectionIds = array_unique($collectionIds);
            $collectionCount = count($collectionIds);
			$pages = ceil((count($collectionIds) / 10));
			$pages ?: $pages = 1;
			$start = $currentPage * 10 - 10;
			$pageIds = array_slice($collectionIds, $start, 10);

			$resultsObjRepo = new CollectionRepository($pageIds);
			$resultsObj = $resultsObjRepo->all()->toArray();
			$results = array();
			foreach ($pageIds as $item){
				$foundKey = array_search($item, array_column($resultsObj, 'id'));
				array_push($results,$resultsObj[$foundKey]);
			}

			//формування масиву із першими зображеннями
			$images = array();
			$count = array();
			$onlyVideos = array();
			$onlyInterviews = array();
			foreach ($results as $result) {
				$paramsSinglePhoto['type'] = 'photo';
				$paramsSinglePhoto['attributes']['lists'] = array();
				$paramsSinglePhoto['relations']['collections']['values'] = array(
					0 => $result->id,
				);
				$paramsSinglePhoto['relations']['collections']['singular'] = 'collection';
				$paramsSinglePhoto['relations']['collections']['plural'] = 'collections';
				$searcher = new Searcher();
				$photoIds =  $searcher->browseObjectSearch($paramsSinglePhoto);
				$count[$result->id] = count($photoIds);

				$paramsSingleVideo['type'] = 'movie';
				$paramsSingleVideo['attributes']['lists'] = array();
				$paramsSingleVideo['relations']['collections']['values'] = array(
                    0 => $result->id,
                );
				$paramsSingleVideo['relations']['collections']['singular'] = 'collection';
				$paramsSingleVideo['relations']['collections']['plural'] = 'collections';
                $searcherVideo = new Searcher();
                $videoIds =  $searcherVideo->browseObjectSearch($paramsSingleVideo);
                $count[$result->id] += count($videoIds);

				$paramsSingleInterview['type'] = 'interview';
				$paramsSingleInterview['attributes']['lists'] = array();
				$paramsSingleInterview['relations']['collections']['values'] = array(
					0 => $result->id,
				);
				$paramsSingleInterview['relations']['collections']['singular'] = 'collection';
				$paramsSingleInterview['relations']['collections']['plural'] = 'collections';
				$searcherInterview = new Searcher();
				$interviewIds =  $searcherInterview->browseObjectSearch($paramsSingleInterview);
				$count[$result->id] += count($interviewIds);

                $onlyVideos[$result->id] = count($photoIds) == 0 && count($videoIds) > 0 ? true : false;
				$onlyInterviews[$result->id] = $result->object_type_id == 254 || $result->object_type_id == 255 ? true : false;

				$images[$result->id] = $resultsObjRepo->get_collection_image($result->id);
				if(empty($images[$result->id]) && isset($result->objects)) {
					$objects = $result->objects;
					foreach ($objects as $object){
						$photoRepo = new ObjectRepository([$object->id]);
						$images[$result->id] = $photoRepo->getPhoto($object->id);
						$images[$result->id] = empty($images[$result->id]) ? '#' : $images[$result->id];
						break;
					}
				}
			}
		}else{
			$collectionTypeCount[121] = false;
			$collectionTypeCount[122] = false;
        	$results = false;
		}

		$queryString = $request->getQueryWithoutPage();
        $slug = 'collections';

		$seo = array();
		$seo['title'] = trans('translations.collections').' | '.trans('translations.og_title');
		$seo['og_title'] = $seo['title'];
		$seo['description'] = trans('translations.collections').' | '.trans('translations.after_description');
		$seo['og_description'] = $seo['description'];

        return view('collections.index',
            compact('results', 'all', 'vocabulary', 'pages', 'queryString', 'currentPage', 'images', 'count', 'slug',
                'collectionCount', 'videoCount', 'photoCount', 'mapCount', 'string', 'interviewCount','collectionIds', 'photoIds', 'mapIds', 'videoIds','collectionType', 'collectionTypeCount',
				'photoFilters', 'mapFilters', 'videoFilters', 'collectionsFilters', 'interviewFilters', 'onlyVideos', 'onlyInterviews', 'seo')
        );
    }

    public function show($id)
    {
        $all['lang'] = lang_code();
        $all['id'] = $id;

        $ids[] = $id;

        $collectionRepo = new CollectionRepository($ids, 122);
        $results = $collectionRepo->all();

        //окремий репо із 4 пов'язаних фото
        $objectsPhotos = $collectionRepo->get_related_objects($results[0]->id, 27)->pluck('object_id')->toArray();
        $objectsPhotosIds = array_slice($objectsPhotos, 0, 4);
        $photos = !empty($objectsPhotosIds) ? new ObjectRepository($objectsPhotosIds) : false;
        $photos = !empty($photos) ? $photos->all() : false;

        //окремий репо із 4 пов'язаних відео
        $objectsVideos = $collectionRepo->get_related_objects($results[0]->id, 25)->pluck('object_id')->toArray();
        $objectsVideosIds = array_slice($objectsVideos, 0, 4);
        $videos = !empty($objectsVideosIds) ? new ObjectRepository($objectsVideosIds) : false;
        $videos = !empty($videos) ? $videos->all() : false;

        //у змінну $image перше зображення із колекції
        $image = $photos->all()[0];
        if (isset($image->representations)) {
            foreach ($image->representations as $representation) {
                $image = media_url($representation->media, 'large');
                break;
            }
        }

		$seo = array();
		$seo['title'] = trans('translations.collection').' | '.trans('translations.og_title');
		$seo['og_title'] = $seo['title'];

        return view('collections.show', ['result' => $results->first(), 'photos' => $photos, 'videos' => $videos, 'image' => $image, 'seo' => $seo]);
    }

	public function photos(PhotoRequest $request,$id)
	{
		$ids[] = $id;

		$collectionRepo = new CollectionRepository($ids, 122);
		$collection = $collectionRepo->all();
		$collection = $collection->first();
		if(empty($collection)){
			$collectionRepo = new CollectionRepository($ids, 121);
			$collection = $collectionRepo->all();
			$collection = $collection->first();
		}

		$params = $request->formParams();
		$params['relations']['collections']['values'] = array(
			0 => $id,
		);
		$params['relations']['collections']['singular'] = 'collection';
		$params['relations']['collections']['plural'] = 'collections';
		$string = $request->only('full-search');
		$string = !empty($string) ? $string['full-search'] : false;
        if(isset($params['attributes']['dates_from_to'])){
            if($params['attributes']['dates_from_to']['value'] == '1600-'.date('Y')){
                unset($params['attributes']['dates_from_to']);
            }
        }

		$searcher = new Searcher();
		$photoIds =  $searcher->browseObjectSearch($params,$string);
		$count = count($photoIds);
		$photoCount = count($photoIds);

		$all = $request->all();
		$all['lang'] = lang_code();
		$currentPage = $request->currentPage();

		$pages = ceil((count($photoIds) / 15));
		$pages ?: $pages = 1;

		$start = $currentPage * 15 - 15;

		$pageIds = array_slice($photoIds, $start, 15);


		if (count($pageIds)) {
			$photoRepo = new ObjectRepository($pageIds, 27);
			$results = $photoRepo->softLoad();
			$placesBase = $photoRepo->getPlaces($all['lang']);
			$places = array();
			foreach ($placesBase as $item){
				$places[$item->object_id] = $item->name;
			}
			$attributesBase = $photoRepo->getAttributes(true,$all['lang'],[97]);
			$attributes = array();
			foreach ($attributesBase as $item){
				$attributes[$item->row_id] = $item->value_longtext1;
			}

			foreach ($results as $result){
				$image = $photoRepo->getPhoto($result->id);
				$result->image = $image;
				$result->year = isset($attributes[$result->id]) ? $attributes[$result->id] : false;
				$result->place = isset($places[$result->id]) ? $places[$result->id] : false;
			}
		}

        $videoParams = $searcher->generate_params('movie', $id);
        $videoIds =  $searcher->browseObjectSearch($videoParams, $string);
        $videoCount = count($videoIds);

        $mapParams = $searcher->generate_params('map', $id);
        $mapIds =  $searcher->browseObjectSearch($mapParams, $string);
        $mapCount = count($mapIds);

		$queryString = sanitizeQueryString($request->getQueryWithoutPage(), 'collection');
		$slug = 'collections/'.$id.'/photos';

		$seo = array();
		$seo['title'] = $collection->name.', '.trans('translations.photo').' | '.trans('translations.og_title');
		$seo['og_title'] = $seo['title'];
		$seo['description'] = trans('translations.collection').', '.trans('translations.photo').' - '.$collection->name.' | '.trans('translations.after_description');
		$seo['og_description'] = $seo['description'];
		$seo['image'] = !empty($photoImages) ? array_slice($photoImages, 0, 1)[0] : false;

		return view('collections.photos',
			compact('collection','count', 'seo','results', 'photoImages', 'vocabulary', 'pages', 'queryString', 'currentPage', 'images', 'slug', 'photoCount', 'videoCount', 'mapCount','id')
		);
	}

	public function videos(VideoRequest $request,$id)
	{
		$ids[] = $id;

		$collectionRepo = new CollectionRepository($ids);
		$collection = $collectionRepo->all();
		$collection = $collection->first();
		$count = count($collection->objects);

		$params = $request->formParams();
		$params['relations']['collections']['values'] = array(
			0 => $id,
		);
		$params['relations']['collections']['singular'] = 'collection';
		$params['relations']['collections']['plural'] = 'collections';
		$string = $request->only('full-search');
		$string = !empty($string) ? $string['full-search'] : false;
        if(isset($params['attributes']['dates_from_to'])){
            if($params['attributes']['dates_from_to']['value'] == '1600-'.date('Y')){
                unset($params['attributes']['dates_from_to']);
            }
        }

		$searcher = new Searcher();
		$videoIds =  $searcher->browseObjectSearch($params,$string);

		$videoCount = count($videoIds);

		$all = $request->all();
		$all['lang'] = lang_code();
		$currentPage = $request->currentPage();

		$pages = ceil((count($videoIds) / 15));
		$pages ?: $pages = 1;

		$start = $currentPage * 15 - 15;

		$pageIds = array_slice($videoIds, $start, 15);


		if (count($videoIds)) {
			$photoRepo = new ObjectRepository($pageIds, 25);
			$results = $photoRepo->softLoad();
			$placesBase = $photoRepo->getPlaces($all['lang']);
			$places = array();
			foreach ($placesBase as $item){
				$places[$item->object_id] = $item->name;
			}
			$attributesBase = $photoRepo->getAttributes(false,false, [97]);
			$attributes = array();
			foreach ($attributesBase as $item){
				$attributes[$item->row_id] = $item->value_longtext1;
			}

			foreach ($results as $result){
				$image = $photoRepo->getPhoto($result->id);
				$result->image = $image;
				$result->year = isset($attributes[$result->id]) ? $attributes[$result->id] : false;
				$result->place = isset($places[$result->id]) ? $places[$result->id] : false;
			}
		}

        $photoParams = $searcher->generate_params('photo',$id);
        $photoIds =  $searcher->browseObjectSearch($photoParams,$string);
        $photoCount = count($photoIds);

        $mapParams = $searcher->generate_params('map',$id);
        $mapIds =  $searcher->browseObjectSearch($mapParams,$string);
        $mapCount = count($mapIds);

		$queryString = sanitizeQueryString($request->getQueryWithoutPage(), 'collection');
		$slug = 'collections/'.$id.'/videos';

		$seo = array();
		$seo['title'] = $collection->name.', '.trans('translations.video').' | '.trans('translations.og_title');
		$seo['og_title'] = $seo['title'];
		$seo['description'] = trans('translations.collection').', '.trans('translations.video').' - '.$collection->name.' | '.trans('translations.after_description');
		$seo['og_description'] = $seo['description'];

		return view('collections.videos',
			compact('collection','count', 'seo','results', 'vocabulary', 'pages', 'queryString', 'currentPage', 'images', 'slug', 'photoCount', 'videoCount', 'mapCount','id')
		);
	}

    public function maps(PhotoRequest $request,$id)
    {
        $ids[] = $id;

        $collectionRepo = new CollectionRepository($ids, 122);
        $collection = $collectionRepo->all();
        $collection = $collection->first();
        if(empty($collection)){
            $collectionRepo = new CollectionRepository($ids, 121);
            $collection = $collectionRepo->all();
            $collection = $collection->first();
        }

        $params = $request->formParams();
        $params['type'] = 'map';
        $params['relations']['collections']['values'] = array(
            0 => $id,
        );
        $params['relations']['collections']['singular'] = 'collection';
        $params['relations']['collections']['plural'] = 'collections';
        $string = $request->only('full-search');
        $string = !empty($string) ? $string['full-search'] : false;
        if(isset($params['attributes']['dates_from_to'])){
            if($params['attributes']['dates_from_to']['value'] == '1600-'.date('Y')){
                unset($params['attributes']['dates_from_to']);
            }
        }

        $searcher = new Searcher();
        $mapIds =  $searcher->browseObjectSearch($params,$string);
        $count = count($mapIds);
        $mapCount = count($mapIds);

        $all = $request->all();
        $all['lang'] = lang_code();
        $currentPage = $request->currentPage();

        $pages = ceil((count($mapIds) / 15));
        $pages ?: $pages = 1;

        $start = $currentPage * 15 - 15;

        $pageIds = array_slice($mapIds, $start, 15);


        if (count($pageIds)) {
            $mapRepo = new ObjectRepository($pageIds, 253);
            $results = $mapRepo->softLoad();
            $placesBase = $mapRepo->getPlaces($all['lang']);
            $places = array();
            foreach ($placesBase as $item){
                $places[$item->object_id] = $item->name;
            }
            $attributesBase = $mapRepo->getAttributes(true,$all['lang'],[97]);
            $attributes = array();
            foreach ($attributesBase as $item){
                $attributes[$item->row_id] = $item->value_longtext1;
            }

            foreach ($results as $result){
                $image = $mapRepo->getPhoto($result->id);
                $result->image = $image;
                $result->year = isset($attributes[$result->id]) ? $attributes[$result->id] : false;
                $result->place = isset($places[$result->id]) ? $places[$result->id] : false;
            }
        }

        $videoParams = $searcher->generate_params('movie', $id);
        $videoIds =  $searcher->browseObjectSearch($videoParams, $string);
        $videoCount = count($videoIds);

        $photoParams = $searcher->generate_params('photo', $id);
        $photoIds =  $searcher->browseObjectSearch($photoParams, $string);
        $photoCount = count($photoIds);

        $queryString = sanitizeQueryString($request->getQueryWithoutPage(), 'collection');
        $slug = 'collections/'.$id.'/maps';

        $seo = array();
        $seo['title'] = $collection->name.', '.trans('translations.map').' | '.trans('translations.og_title');
        $seo['og_title'] = $seo['title'];
        $seo['description'] = trans('translations.collection').', '.trans('translations.map').' - '.$collection->name.' | '.trans('translations.after_description');
        $seo['og_description'] = $seo['description'];
        $seo['image'] = !empty($photoImages) ? array_slice($photoImages, 0, 1)[0] : false;

        return view('collections.map',
            compact('collection','count', 'seo','results', 'photoImages', 'vocabulary', 'pages', 'queryString', 'currentPage', 'images', 'slug', 'photoCount', 'videoCount', 'mapCount','id')
        );
    }

	public function interviews(InterviewRequest $request,$id){
		$ids[] = $id;

		$collectionRepo = new CollectionRepository($ids);
		$collection = $collectionRepo->all();
		$collection = $collection->first();


		$user = isset($_COOKIE['user-id']) ? $_COOKIE['user-id'] : false;
		$user = DB::connection('mysql_users')->table('users')->where('id', $user)->get()->toArray();
		$user = !empty($user) && is_array($user) ? $user[0] : $user;
		$allow = $this->isUserAllow($user);

		$all = $request->all();
		$all['collections'] = array(
			$id => $id
		);
		$params = getAllFilters($all);
		$searcher = new Searcher();
		$string = $request->only('full-search');
		$string = !empty($string) ? $string['full-search'] : false;

		$interviewIds = $searcher->browseObjectSearch($params['params']['interview'],$string);
		$all['lang'] = lang_code();
		$queryString = sanitizeQueryString($request->getQueryWithoutPage());
		$currentPage = $request->currentPage();
		$pages = ceil((count($interviewIds) / 6));
		$pages ?: $pages = 1;
		$start = $currentPage * 6 - 6;

		$pageIds = array_slice($interviewIds, $start, 6);
		$interviewRepo = new ObjectRepository($pageIds, 247);
		if(count($interviewRepo->getObjectIdsByCollection(247,$id)) > 0 || count($interviewRepo->getObjectIdsByCollection(255,$id)) > 0) {
			$results = $interviewRepo->softLoad();
			$attributesBaseDate = $interviewRepo->getAttributes(true, $all['lang'], [96]);
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
				$result->date = !empty($attributesDate[$result->id]) ? $this->interview_date($attributesDate[$result->id]) : false;
				$result->description = !empty($attributesDescription[$result->id]) ? $attributesDescription[$result->id] : false;
			}
		}else{
			$results = false;
		}

		$photoFilters = $params['filters']['photo'];
		$videoFilters = $params['filters']['video'];
		$collectionsFilters = $params['filters']['collection'];

		$videoIds =  $searcher->browseObjectSearch($params['params']['video'],$string);
		$videoCount = count($videoIds);

		$photoIds =  $searcher->browseObjectSearch($params['params']['photo'],$string);
		$photoCount = count($photoIds);

		$collectionPhotoIDs = DB::table('ca_objects_x_collections')->whereIn('object_id', $photoIds)->pluck('collection_id')->toArray();
		$collectionVideosIDs = DB::table('ca_objects_x_collections')->whereIn('object_id', $videoIds)->pluck('collection_id')->toArray();
		$collectionIds = !empty($collectionPhotoIDs) ? array_unique($collectionPhotoIDs) : array(0);
		$collectionIds = !empty($collectionVideosIDs) ? array_merge(array_unique($collectionVideosIDs), $collectionIds) : $collectionIds;
		if(isset($all['collection_type']) && !empty($all['collection_type'])){
			$repoCollection = new CollectionRepository([]);
			$collectionIds = !empty($collectionType) && !is_array($collectionType) ? $repoCollection->get_collections_by_type($collectionType, $collectionIds, 'ids') : $collectionIds;
		}
		$collectionCount = count($collectionIds);

		$slug = 'collections/'.$id.'/interviews';

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

		$seo = array();
		$seo['title'] = trans('translations.interview').' | '.trans('translations.og_title');
		$seo['og_title'] = $seo['title'];
		$seo['description'] = trans('translations.interview').' | '.trans('translations.after_description');
		$seo['og_description'] = $seo['description'];

		return view('collections.interview',
			compact('results', 'collection', 'interviewCollections' ,'pages', 'queryString', 'currentPage', 'photoCount', 'videoCount','collectionCount', 'slug'
				, 'photoFilters', 'videoFilters', 'collectionsFilters', 'all', 'allow', 'seo', 'user', 'id')
		);
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
