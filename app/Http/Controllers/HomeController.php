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

class HomeController extends ObjectController
{
    public function index(PhotoRequest $request)
    {
		$lang = lang_code();
		$langCode = $lang == 1 ? 'en' : 'uk';
        $url = 'https://www.lvivcenter.org/wp-json/uma/v1/json';
        $pageDocument = @file_get_contents($url);
        if($pageDocument){
            $json = file_get_contents($url);
            $messages = json_decode($json, true);
            $messages = !empty($messages) && isset($messages[$langCode]) ? $messages[$langCode] : false;
        }else{
            $messages = false;
        }

        //отримати чотири фото для списку
        $photoRepo = new ObjectRepository([]);
        $ids = $photoRepo->objectsByAttribute(118,246);
        $ids = !empty($ids) ? $ids : [9,34];
        $featuredPhoto = new ObjectRepository($ids, 27);
        $featuredPhoto = $featuredPhoto->all();
        $featuredIds = array();
        foreach ($featuredPhoto as $featured){
        	$featured->image = $photoRepo->getPhoto($featured->id, 'large');
        	array_push($featuredIds,$featured->id);
		}
        $featuredOrder = range(0, count($featuredIds) - 1);
        shuffle($featuredOrder);

        //витягнути останні додані об'єкти
        $lastObjectsIDs = $photoRepo->getLastObjects(4);
		$lastObjectsRepo = new ObjectRepository($lastObjectsIDs);
		$lastObjects = $lastObjectsRepo->softLoad();
		$lastObjectsLink = 'photos';
		$lastObjectsTypes = array(
			'photos' => 0,
			'videos' => 0,
			'interviews' => 0,
            'maps' => 0
		);
        foreach ($lastObjects as $object){
			$object->image = $photoRepo->getPhoto($object->id);
			$place = $photoRepo->getPlaces($lang,[$object->id]);
			$object->place = !empty($place) && count($place) > 0 ? $place[0]->name : false;
			$year = $photoRepo->getAttributes(true, 1, [97], [$object->id]);
			$object->year = !empty($year) && count($year) > 0 ? $year[count($year) - 1]->value_longtext1 : false;
			if($object->object_type_id == 247){
                $description = $photoRepo->getAttributes(true, lang_code(), [38], [$object->id]);
                $object->description = !empty($description) && isset($description[0]) > 0 ? $description[0]->value_longtext1 : false;
            }
            $year = $photoRepo->getAttributes(true, 1, [97], [$object->id]);
			switch ($object->object_type_id){
				case 27:
					$lastObjectsTypes['photos']++;
					break;
				case 25:
					$lastObjectsTypes['videos']++;
					break;
				case 247:
					$lastObjectsTypes['interviews']++;
					break;
                case 253:
                    $lastObjectsTypes['maps']++;
                    break;
			}
		}
		$lastObjectsMax = 0;
		foreach($lastObjectsTypes as $key => $type){
			if($type > $lastObjectsMax){
				$lastObjectsMax = $type;
				$lastObjectsLink = $key;
			}
		}

		$bannerImages = array_diff(scandir('img/home_banner'), array('..', '.'));
        $banner = false;
        if(count($bannerImages) > 0){
			$bannerKey = array_rand($bannerImages, 1);
			$banner = 'img/home_banner/'.$bannerImages[$bannerKey];
		}

        //підпахунок кількості об'єктів
        $all = $request->all();
        $params = getAllFilters($all);

        $countSearcher = new Searcher();
        $counts = array();
        $videoIds = $countSearcher->browseObjectSearch($params['params']['video']);
        $counts['videos'] = count($videoIds);
        $photoIds =  $countSearcher->browseObjectSearch($params['params']['photo']);
        $counts['photos'] = count($photoIds);
        $mapIds =  $countSearcher->browseObjectSearch($params['params']['map']);
        $counts['maps'] = count($mapIds);
        $interviewIds =  $countSearcher->browseObjectSearch($params['params']['interview']);
        $counts['interviews'] = count($interviewIds);
        $collectionPhotoIDs = DB::table('ca_objects_x_collections')->whereIn('object_id', $photoIds)->pluck('collection_id')->toArray();
        $collectionVideosIDs = DB::table('ca_objects_x_collections')->whereIn('object_id', $videoIds)->pluck('collection_id')->toArray();
        $collectionIds = !empty($collectionPhotoIDs) ? array_unique($collectionPhotoIDs) : array(0);
        $collectionIds = !empty($collectionVideosIDs) ? array_merge(array_unique($collectionVideosIDs), $collectionIds) : $collectionIds;
        $counts['collections'] = count($collectionIds);

		$seo = array();
		$seo['title'] = trans('translations.og_title');
		$seo['og_title'] = $seo['title'];
		$seo['description'] = trans('translations.og_title').' | '.trans('translations.after_description');
		$seo['og_description'] = $seo['description'];

        return view('home',
            compact('messages', 'counts', 'featuredPhoto', 'featuredOrder', 'lastObjects', 'lastObjectsLink', 'lang', 'banner', 'seo')
        );
    }

    public function about(PhotoRequest $request){

        $all = $request->all();
        $params = getAllFilters($all);

        $countSearcher = new Searcher();
        $videoIds = $countSearcher->browseObjectSearch($params['params']['video']);
        $videoCount = count($videoIds);
        $photoIds =  $countSearcher->browseObjectSearch($params['params']['photo']);
        $photoCount = count($photoIds);
        $interviewIds =  $countSearcher->browseObjectSearch($params['params']['interview']);
        $interviewCount = count($interviewIds);
        $mapIds =  $countSearcher->browseObjectSearch($params['params']['map']);
        $mapCount = count($mapIds);

		$repo = new CollectionRepository([]);
		$collectionIds = array_column($repo->get_collections(),'collection_id');
		$collectionCount = count($collectionIds);

		$seo = array();
		$seo['title'] = trans('translations.about_us').' | '.trans('translations.og_title');
		$seo['og_title'] = $seo['title'];
		$seo['description'] = trans('translations.about_us').' | '.trans('translations.after_description');
		$seo['og_description'] = $seo['description'];

		return view('about',
			compact('videoCount','photoCount', 'collectionCount', 'interviewCount', 'mapCount', 'seo')
		);
	}

	public function faq(){
		$seo = array();
		$seo['title'] = trans('translations.faq').' | '.trans('translations.og_title');
		$seo['og_title'] = $seo['title'];
		$seo['description'] = trans('translations.faq').' | '.trans('translations.after_description');
		$seo['og_description'] = $seo['description'];

		return view('faq',
			compact('videoCount','photoCount', 'collectionCount', 'seo')
		);
	}
}
