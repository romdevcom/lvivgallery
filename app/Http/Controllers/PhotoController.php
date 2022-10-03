<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhotoRequest;
use App\Repositories\CollectionRepository;
use App\Repositories\EntityRepository;
use App\Repositories\ObjectRepository;
use App\Repositories\PlaceRepository;
use App\Searcher;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
//use App\Http\DeepzoomFactory;

class PhotoController extends ObjectController
{
    public function index(PhotoRequest $request)
    {
		$all = $request->all();
        $params = getAllFilters($all);
		$searcher = new Searcher();
		$string = $request->only('full-search');
		$string = !empty($string) ? $string['full-search'] : false;
        $withoutPage = $all;

        $oldPlaces = isset($params['params']['photo']['relations']['places']['values']) ? $params['params']['photo']['relations']['places']['values'] : array();
        $oldCollections = isset($params['params']['photo']['relations']['collections']['values']) ? $params['params']['photo']['relations']['collections']['values'] : array();
        $oldTechniques = isset($params['params']['photo']['attributes']['lists']['technique']) ? $params['params']['photo']['attributes']['lists']['technique'] : array();
        $oldDates = isset($params['params']['photo']['attributes']['dates_from_to']['value']) ? $params['params']['photo']['attributes']['dates_from_to']['value'] : false;
        $oldDates = $oldDates ? explode(',', $oldDates) : array();

        unset($withoutPage['page']);
        if(empty($withoutPage)){
            $hash = md5('photos');
            $photoIds = DB::table('sg_object_hash')->where([['hash',$hash],['created_at', '>=', \Carbon\Carbon::now()->subHours(4)]])->pluck('ids')->toArray();
            if(empty($photoIds)){
                $photoIds = $searcher->browseObjectSearch($params['params']['photo'], $string);
                shuffle($photoIds);
                DB::table('sg_object_hash')->insert(
                    ['hash' => $hash, 'ids' => implode(',',$photoIds)]
                );
                DB::table('sg_object_hash')->where([['hash',$hash],['created_at', '<', \Carbon\Carbon::now()->subHours(4)]])->delete();

            }else{
                $photoIds = array_map('intval', explode(',', $photoIds[0]));
                DB::table('sg_object_hash')->where([['hash',$hash],['created_at', '<', \Carbon\Carbon::now()->subHours(4)]])->delete();
            }
        }else{
            if(isset($params['params']['photo']['attributes']['dates_from_to'])){
                if($params['params']['photo']['attributes']['dates_from_to']['value'] == '1600-'.date('Y')){
                    unset($params['params']['photo']['attributes']['dates_from_to']);
                }
            }
            $photoIds = $searcher->browseObjectSearch($params['params']['photo'], $string);
        }
        $photoCount = count($photoIds);

		$videoFilters = $params['filters']['video'];
		$mapFilters = $params['filters']['map'];
		$collectionsFilters = $params['filters']['collection'];
		$interviewFilters = $params['filters']['interview'];
		$photoFilters = '?'.$request->getQueryWithoutPage();
        $photoFilters = !empty($photoFilters) ? rtrim($photoFilters,'&') : '';
        $mapFilters = !empty($mapFilters) ? rtrim($mapFilters,'&') : '';
        $interviewFilters = !empty($interviewFilters) ? rtrim($interviewFilters,'&') : '';
        $videoFilters = !empty($videoFilters) ? rtrim($videoFilters,'&') : '';
        $collectionsFilters = !empty($collectionsFilters) ? rtrim($collectionsFilters,'&') : '';


        $all['lang'] = lang_code();
		$queryString = sanitizeQueryString($request->getQueryWithoutPage());

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

		//count of photo with filters
		$videoIds =  $searcher->browseObjectSearch($params['params']['video'],$string);
		$videoCount = count($videoIds);

        $mapIds =  $searcher->browseObjectSearch($params['params']['map'],$string);
        $mapCount = count($mapIds);

		$interviewIds =  $searcher->browseObjectSearch($params['params']['interview'],$string);
		$interviewCount = count($interviewIds);

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

		$slug = 'photos';

		$seo = array();
		$seo['title'] = trans('translations.photo').' | '.trans('translations.og_title');
		$seo['og_title'] = $seo['title'];
		$seo['description'] = trans('translations.photo').' | '.trans('translations.after_description');
		$seo['og_description'] = $seo['description'];
        $seo['image'] = 'https://uma.lvivcenter.org/img/uma-filter-photos.jpg';

        return view('photos.index',
            compact('results', 'pages',
                'queryString',
                'currentPage',
                'photoCount', 'mapCount', 'videoCount','collectionCount', 'interviewCount',
                'slug',
                'oldPlaces', 'oldCollections', 'oldTechniques', 'oldDates', 'string',
                'photoIds', 'mapIds', 'videoIds', 'collectionIds',
                'photoFilters', 'mapFilters', 'videoFilters', 'collectionsFilters', 'interviewFilters',
                'seo')
        );
    }

    public function show($id)
    {
        $all['lang'] = lang_code();
        $all['id'] = $id;
        $all['type'] = 'photo';

        $ids[] = $id;

//        if($id == 34272){
//            $log = DB::table('ca_objects as o')
//                ->select('o.object_id','o.type_id', 'l.log_datetime')
//                ->join('ca_change_log as l', 'o.object_id', 'l.logged_row_id')
//                ->where([['o.object_id', $id]])
//                ->orderBy('l.log_datetime', 'DESC')
//			    ->orderBy('l.log_id', 'DESC')
//                ->get();
//            dd($log);
//        }

        $photoRepo = new ObjectRepository($ids, 27, true);
        $results = $photoRepo->all();
        $first = $results->first();

        if(empty($first)) return redirect('photos');

        $first->creator = false;
        $first->publisher = false;
        $first->rights = false;
        if(!empty($first->entities)){
        	foreach($first->entities as $entity){
        		switch ($entity->type_id){
					case 202:
						$first->creator = $entity->name;
						$first->creator_id = $entity->id;
						break;
					case 112:
						$first->publisher = $entity->name;
						break;
					case 201:
						$first->rights = $entity->name;
						break;
				}
			}
		}

        if(empty($first->technique)) {
            $attributesTechniqueBase = $photoRepo->getAttributes(true, 1, [95], [$id]);
            $first->technique = count($attributesTechniqueBase) > 0 && !empty($attributesTechniqueBase[0]->value_longtext1) ? trans('translations.technique_'.$attributesTechniqueBase[0]->value_longtext1) : false;
            if(empty($first->technique)){
                $attributesTechniqueBase = $photoRepo->getAttributes(true, 15, [95], [$id]);
                $first->technique = count($attributesTechniqueBase) > 0 && !empty($attributesTechniqueBase[0]->value_longtext1) ? trans('translations.technique_'.$attributesTechniqueBase[0]->value_longtext1) : false;
            }
        }

        $seo = array();
        $seo['title'] = $first->name.' | '.trans('translations.og_title');
		$seo['og_title'] = $seo['title'];
        $seo['description'] = $first->name.' | '.trans('translations.after_description');
		$seo['og_description'] = $seo['description'];

		if(!empty($first->representations)){
			foreach ($first->representations as $representation){
				$seo['image'] = media_url($representation->media, 'large');
			}
		}

//		dd($first);

		$gallery = $photoRepo->get_gallery_for_photo($id);

        $related = $photoRepo->relatedForSinglePhoto();
        foreach ($related as $value){
            $attributesBase = $photoRepo->getAttributes(true,1,[97],[$value->id]);
            $attributes = array();
            foreach ($attributesBase as $item){
                $attributes[$item->row_id] = $item->value_longtext1;
            }
            $value->year = isset($attributes[$value->id]) ? $attributes[$value->id] : false;
        }

        return view('photos.show', ['result' => $first, 'related' => $related, 'gallery' => $gallery, 'seo' => $seo]);
    }

    public function deepzoom($id){
		$ids[] = $id;
		$photoRepo = new ObjectRepository($ids, 27);
		$results = $photoRepo->all();
		$results = $results->first();
		$image = $results->representations;
		foreach ($image as $item){
			$image = $item->media;
			break;
		}
		$image = media_url($image, 'large');

		$deepzoomRepo = new DeepzoomFactory();
		$deepzoom =$deepzoomRepo->create([
			'path' => 'http://histock.local/storage/collectiveaccess/images/3/5/',
			'driver' => 'imagick',
			'format' => 'jpg',
		]);
		$response = $deepzoom->makeTiles('6562_ca_object_representations_media_3507_large.jpg', 'file', 'folder');

    	dd($response);
	}

//    public function get_object_data($id){
//        $ids[] = $id;
//        $photoRepo = new ObjectRepository($ids, 27, true);
//        $results = $photoRepo->all();
//        $first = $results->first();
//        var_dump($first);
//        $result = array(
//            'id' => $id,
////            'image' => $image
//        );
//        return json_encode($result);
//    }
}
