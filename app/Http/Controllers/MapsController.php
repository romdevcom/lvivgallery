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
use App\Http\DeepzoomFactory;

class MapsController extends ObjectController
{
    public function index(PhotoRequest $request)
    {
        $all = $request->all();
        $params = getAllFilters($all);
        $searcher = new Searcher();
        $string = $request->only('full-search');
        $string = !empty($string) ? $string['full-search'] : false;

        $oldPlaces = isset($params['params']['map']['relations']['places']['values']) ? $params['params']['map']['relations']['places']['values'] : array();
        $oldCollections = isset($params['params']['map']['relations']['collections']['values']) ? $params['params']['map']['relations']['collections']['values'] : array();
        $oldDates = isset($params['params']['map']['attributes']['dates_from_to']['value']) ? $params['params']['map']['attributes']['dates_from_to']['value'] : false;
        $oldDates = $oldDates ? explode(',', $oldDates) : array();

        $withoutPage = $all;
        unset($withoutPage['page']);
        $mapsIds = $searcher->browseObjectSearch($params['params']['map'], $string);
        $mapCount = count($mapsIds);

        $videoFilters = $params['filters']['video'];
        $collectionsFilters = $params['filters']['collection'];
        $interviewFilters = $params['filters']['interview'];
        $photoFilters = $params['filters']['photo'];
        $mapFilters = $params['filters']['map'];
        $photoFilters = !empty($photoFilters) ? rtrim($photoFilters,'&') : '';
        $mapFilters = !empty($mapFilters) ? rtrim($mapFilters,'&') : '';
        $videoFilters = !empty($videoFilters) ? rtrim($videoFilters,'&') : '';
        $collectionsFilters = !empty($collectionsFilters) ? rtrim($collectionsFilters,'&') : '';


        $all['lang'] = lang_code();
        $queryString = sanitizeQueryString($request->getQueryWithoutPage());

        $currentPage = $request->currentPage();

        $pages = ceil((count($mapsIds) / 15));
        $pages ?: $pages = 1;

        $start = $currentPage * 15 - 15;

        $mapsIds = array_slice($mapsIds, $start, 15);
        if (count($mapsIds)) {
            $mapRepo = new ObjectRepository($mapsIds, 253);
            $results = $mapRepo->softLoad();
            $placesBase = $mapRepo->getPlaces($all['lang']);
            $places = array();
            foreach ($placesBase as $item){
                $places[$item->object_id] = $item->name;
            }
            $attributesBase = $mapRepo->getAttributes(true, 15, [97]);
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

        //count of photo with filters
        $videoIds =  $searcher->browseObjectSearch($params['params']['video'],$string);
        $videoCount = count($videoIds);

        $photoIds =  $searcher->browseObjectSearch($params['params']['photo'],$string);
        $photoCount = count($photoIds);

        $interviewIds =  $searcher->browseObjectSearch($params['params']['interview'],$string);
        $interviewCount = count($interviewIds);

        $collectionPhotoIDs = DB::table('ca_objects_x_collections')->whereIn('object_id', $photoIds)->pluck('collection_id')->toArray();
        $collectionMapIDs = DB::table('ca_objects_x_collections')->whereIn('object_id', $mapsIds)->pluck('collection_id')->toArray();
        $collectionVideosIDs = DB::table('ca_objects_x_collections')->whereIn('object_id', $videoIds)->pluck('collection_id')->toArray();
        $collectionIds = !empty($collectionPhotoIDs) ? array_unique($collectionPhotoIDs) : array(0);
        $collectionIds = !empty($collectionVideosIDs) ? array_merge(array_unique($collectionVideosIDs), $collectionIds) : $collectionIds;
        $collectionIds = !empty($collectionMapIDs) ? array_merge(array_unique($collectionMapIDs), $collectionIds) : $collectionIds;

        if(isset($all['collection_type']) && !empty($all['collection_type'])){
            $repoCollection = new CollectionRepository([]);
            $collectionIds = !empty($collectionType) && !is_array($collectionType) ? $repoCollection->get_collections_by_type($collectionType, $collectionIds, 'ids') : $collectionIds;
        }

        $collectionCount = count(array_unique($collectionIds));

        $slug = 'maps';

        $seo = array();
        $seo['title'] = trans('translations.maps').' | '.trans('translations.og_title');
        $seo['og_title'] = $seo['title'];
        $seo['description'] = trans('translations.maps').' | '.trans('translations.after_description');
        $seo['og_description'] = $seo['description'];
        $seo['image'] = 'https://uma.lvivcenter.org/img/uma-filter-maps.jpg';

//        dd($results);

        return view('maps.index',
            compact('results',
                'pages',
                'queryString',
                'currentPage',
                'mapCount', 'photoCount', 'videoCount','collectionCount', 'interviewCount',
                'slug',
                'oldPlaces', 'oldCollections', 'oldDates', 'string',
                'photoIds', 'videoIds', 'collectionIds',
                'mapFilters', 'photoFilters', 'videoFilters', 'collectionsFilters', 'interviewFilters',
                'seo')
        );
    }

    public function show($id)
    {
        $all['lang'] = lang_code();
        $all['id'] = $id;
        $all['type'] = 'map';

        $ids[] = $id;

        $photoRepo = new ObjectRepository($ids, 253, true);
        $results = $photoRepo->all();
        $first = $results->first();

        if(empty($first)) return redirect('maps');

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
        }

        $seo = array();
        $seo['title'] = $first->name.' | '.trans('translations.og_title');
        $seo['og_title'] = $seo['title'];
        $seo['description'] = $first->name.' | '.trans('translations.after_description');
        $seo['og_description'] = $seo['description'];

//        dd($first->representations);
        if(!empty($first->representations)){
            foreach ($first->representations as $representation){
                $seo['image'] = media_url($representation->media, 'large');
            }
        }

        $gallery = $photoRepo->get_gallery_for_photo($id);

//        $related = $photoRepo->relatedForSinglePhoto(253);
//        foreach ($related as $value){
//            $attributesBase = $photoRepo->getAttributes(true,1,[97],[$value->id]);
//            $attributes = array();
//            foreach ($attributesBase as $item){
//                $attributes[$item->row_id] = $item->value_longtext1;
//            }
//            $value->year = isset($attributes[$value->id]) ? $attributes[$value->id] : false;
//        }
        $related = false;

//        dd($first);

        return view('maps.show', ['result' => $first, 'related' => $related, 'gallery' => $gallery, 'seo' => $seo]);
    }
}
