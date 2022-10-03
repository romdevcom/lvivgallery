<?php

namespace App\Http\Controllers;

use App\Http\Requests\VideoRequest;
use App\Http\Requests\PhotoRequest;
use App\Http\Requests\CollectionRequest;
use App\Repositories\CollectionRepository;
use App\Repositories\EntityRepository;
use App\Repositories\ObjectRepository;
use App\Repositories\PlaceRepository;
use App\Searcher;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class VideoController extends ObjectController
{
    public function index(VideoRequest $request)
    {
		$string = $request->only('full-search');
		$string = !empty($string) ? $string['full-search'] : false;
		$searcher = new Searcher();

		$all = $request->all();
        $params = getAllFilters($all);
		$withoutPage = $all;

        $oldPlaces = isset($params['params']['video']['relations']['places']['values']) ? $params['params']['video']['relations']['places']['values'] : array();
        $oldCollections = isset($params['params']['video']['relations']['collections']['values']) ? $params['params']['video']['relations']['collections']['values'] : array();
        $oldTechniques = isset($params['params']['video']['attributes']['lists']['movie_technique']) ? $params['params']['video']['attributes']['lists']['movie_technique'] : array();;
        $oldGenre = isset($params['params']['video']['attributes']['lists']['movie_genre']) ? $params['params']['video']['attributes']['lists']['movie_genre'] : array();;
        $oldDates = isset($params['params']['video']['attributes']['dates_from_to']['value']) ? $params['params']['video']['attributes']['dates_from_to']['value'] : false;
        $oldDates = $oldDates ? explode(',', $oldDates) : array();

        unset($withoutPage['page']);
		if(empty($withoutPage)){
			$hash = md5('videos');
			$videoIds = DB::table('sg_object_hash')->where([['hash',$hash],['created_at', '>=', \Carbon\Carbon::now()->subHours(4)]])->pluck('ids')->toArray();
			if(empty($videoIds)){
				$videoIds = $searcher->browseObjectSearch($params['params']['video'], $string);
				shuffle($videoIds);
				DB::table('sg_object_hash')->insert(
					['hash' => $hash, 'ids' => implode(',',$videoIds)]
				);
				DB::table('sg_object_hash')->where([['hash',$hash],['created_at', '<', \Carbon\Carbon::now()->subHours(4)]])->delete();

			}else{
				$videoIds = array_map('intval', explode(',', $videoIds[0]));
				DB::table('sg_object_hash')->where([['hash',$hash],['created_at', '<', \Carbon\Carbon::now()->subHours(4)]])->delete();
			}
		}else{
		    if(isset($params['params']['video']['attributes']['dates_from_to'])){
		        if($params['params']['video']['attributes']['dates_from_to']['value'] == '1600-'.date('Y')){
                    unset($params['params']['video']['attributes']['dates_from_to']);
                }
            }
			$videoIds = $searcher->browseObjectSearch($params['params']['video'], $string);
		}

		//make filters (collections, date, place, full-search)
		$photoFilters = $params['filters']['photo'];
        $mapFilters = $params['filters']['map'];
		$collectionsFilters = $params['filters']['collection'];
		$interviewFilters = $params['filters']['interview'];
		$videoFilters = '?'.$request->getQueryWithoutPage();
        $photoFilters = !empty($photoFilters) ? rtrim($photoFilters,'&') : '';
        $mapFilters = !empty($mapFilters) ? rtrim($mapFilters,'&') : '';
        $collectionsFilters = !empty($collectionsFilters) ? rtrim($collectionsFilters,'&') : '';
        $interviewFilters = !empty($interviewFilters) ? rtrim($interviewFilters,'&') : '';

		$all['lang'] = lang_code();
		$queryString = sanitizeQueryString($request->getQueryWithoutPage());
		$currentPage = $request->currentPage();

		$pages = ceil((count($videoIds) / 15));
		$pages ?: $pages = 1;

		$start = $currentPage * 15 - 15;

		$videoCount = count($videoIds);

		$pageIds = array_slice($videoIds, $start, 15);

		if (count($pageIds)) {
			$videoRepo = new ObjectRepository($pageIds, 25);
			$results = $videoRepo->softLoad();
			$placesBase = $videoRepo->getPlaces($all['lang']);
			$places = array();
			foreach ($placesBase as $item){
				$places[$item->object_id] = $item->name;
			}
			$attributesBase = $videoRepo->getAttributes(false,false, [97]);
			$attributes = array();
			foreach ($attributesBase as $item){
				$attributes[$item->row_id] = $item->value_longtext1;
			}

			foreach ($results as $result){
				$image = $videoRepo->getPhoto($result->id);
				$result->image = $image;
				$result->year = isset($attributes[$result->id]) ? $attributes[$result->id] : false;
				$result->place = isset($places[$result->id]) ? $places[$result->id] : false;
			}
		}

		//count of photo with filters
		$photoIds =  $searcher->browseObjectSearch($params['params']['photo'],$string);
		$photoCount = count($photoIds);
		$interviewIds =  $searcher->browseObjectSearch($params['params']['interview'],$string);
		$interviewCount = count($interviewIds);
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

		$seo = array();
		$seo['title'] = trans('translations.videos_and_movies').' | '.trans('translations.og_title');
		$seo['og_title'] = $seo['title'];
		$seo['description'] = trans('translations.video').' | '.trans('translations.after_description');
		$seo['og_description'] = $seo['description'];
		$seo['image'] = 'https://uma.lvivcenter.org/img/uma-filter-video.jpg';

		$slug = 'videos';
		return view('videos.index',
			compact('results',
                'photoVideos',
                'vocabulary',
                'pages',
                'seo',
                'photoIds', 'mapIds', 'videoIds', 'collectionIds',
                'queryString',
                'currentPage',
                'oldPlaces', 'oldCollections', 'oldTechniques', 'oldGenre', 'oldDates', 'string',
                'videoCount', 'photoCount', 'mapCount', 'collectionCount', 'interviewCount',
                'slug',
				'photoFilters', 'mapFilters', 'videoFilters', 'collectionsFilters', 'interviewFilters')
		);
    }

	public function show($id)
	{
		$all['lang'] = lang_code();
		$all['id'] = $id;
		$all['type'] = 'photo';

		$ids[] = $id;
		$videoRepo = new ObjectRepository($ids, 25,true);

		$results = $videoRepo->all();
		$first = $results->first();
        $first->name = !empty($first->name) ? $first->name : $id;
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

		if(empty($first)) return abort(404);

        if(empty($first->technique)) {
            $attributesTechniqueBase = $videoRepo->getAttributes(true, 1, [105], [$id]);
            $first->technique = count($attributesTechniqueBase) > 0 && !empty($attributesTechniqueBase[0]->value_longtext1) ? trans('translations.technique_'.$attributesTechniqueBase[0]->value_longtext1) : false;
            if(empty($first->technique)){
                $attributesTechniqueBase = $videoRepo->getAttributes(true, 15, [105], [$id]);
                $first->technique = count($attributesTechniqueBase) > 0 && !empty($attributesTechniqueBase[0]->value_longtext1) ? trans('translations.technique_'.$attributesTechniqueBase[0]->value_longtext1) : false;
            }
        }
        if(empty($first->movie_genre)) {
            $attributesGenreBase = $videoRepo->getAttributes(true, 1, [104], [$id]);
            $attributesGenreId = false;
            if(count($attributesGenreBase) > 0){
                foreach ($attributesGenreBase as $base){
                    if(!empty($base->value_longtext1)){
                        $attributesGenreId = $base->value_longtext1;
                        break;
                    }
                }
            }
            if(empty($attributesGenreId)){
                $attributesGenreBase = $videoRepo->getAttributes(true, 15, [104], [$id]);
                foreach ($attributesGenreBase as $base){
                    if(!empty($base->value_longtext1)){
                        $attributesGenreId = $base->value_longtext1;
                        break;
                    }
                }
            }
            $first->movie_genre = !empty($attributesGenreId) ? $videoRepo->get_list_label($attributesGenreId, $all['lang']) : false;
        }

        $related = $videoRepo->relatedForSingleVideo();
        $relatedImages = array();
        foreach ($related as $value){
            $relatedImages[$value->id] = $videoRepo->getPhoto($value->id);
            $attributesBase = $videoRepo->getAttributes(true,1,[97],[$value->id]);
            $attributes = array();
            foreach ($attributesBase as $item){
                $attributes[$item->row_id] = $item->value_longtext1;
            }
            $value->year = isset($attributes[$value->id]) ? $attributes[$value->id] : false;
        }

		$seo = array();
		$seo['title'] = $first->name.' | '.trans('translations.og_title');
		$seo['og_title'] = $seo['title'];
		$seo['description'] = $first->name.' | '.trans('translations.after_description');
		$seo['og_description'] = $seo['description'];
		$seo['image'] = $videoRepo->getPhoto($id);

		return view('videos.show', ['result' => $first, 'related' => $related, 'relatedImages' => $relatedImages, 'seo' => $seo]);
	}
}
