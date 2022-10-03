<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhotoRequest;
use App\Http\Requests\VideoRequest;
use App\Repositories\CollectionRepository;
use App\Repositories\EntityRepository;
use App\Repositories\ObjectRepository;
use App\Repositories\PlaceRepository;
use App\Searcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
//use App\Http\Controller\ZoomifyFileProcessor;

class ObjectController extends Controller
{
	protected function makeCacheKey($params)
	{
		$string = '';

		$string .= $this->paramsString($params);

		return hash('md5', $string);
	}

	protected function paramsString($params)
	{
		$string = '';
		foreach ($params as $param) {
			if (is_array($param)) {
				$string .= $this->paramsString($param);
			} else {
				$string .= $param;
			}
		}

		return $string;
	}

	public function random()
	{
		$int = 20;/*rand(1, 100);*/
		$res = [];

		for ($i = 0; $i < $int; $i++) {
			$res[] = rand(1, 10000);
		}

		return $res;
	}

	public function get_object_data_ids(){
        $results = array();
	    if(isset($_REQUEST['ids'])){
	        $ids = $_REQUEST['ids'];
            $ids = explode(',', $ids);
            foreach ($ids as $id){
                $result = $this->get_api_object_by_id($id);
                if(!empty($result)){
                    array_push($results, $result);
                }
            }
        }
        $result = preg_replace_callback('/\\\\u(\w{4})/', function ($matches) {
            return html_entity_decode('&#x' . $matches[1] . ';', ENT_COMPAT, 'UTF-8');
        }, json_encode($results));

        return response($result)->header('Content-Type', 'application/json; charset=UTF-8');
    }

	public function get_object_data($id){
        $result = array();
        if(!empty($id)) {
            $result = $this->get_api_object_by_id($id);
        }
        $result = preg_replace_callback('/\\\\u(\w{4})/', function ($matches) {
            return html_entity_decode('&#x' . $matches[1] . ';', ENT_COMPAT, 'UTF-8');
        }, json_encode($result));
        return response($result)->header('Content-Type', 'application/json; charset=UTF-8');
    }

    public function get_api_object_by_id($id){
        if(!empty($id)) {
            //titles
            $titleUk = false;
            $titleEn = false;
            $titlesRepo = DB::table('ca_object_labels')
                ->where('object_id', $id)
                ->get();
            if (!empty($titlesRepo)) {
                foreach ($titlesRepo as $title) {
                    if ($title->locale_id == 1) $titleEn = $title->name;
                    if ($title->locale_id == 15) $titleUk = $title->name;
                }
            }

            //image
            $image = false;
            $representationsRepo = DB::table('ca_object_representations as r')
                ->leftJoin('ca_objects_x_object_representations as x', function ($join) {
                    $join->on('x.representation_id', '=', 'r.representation_id');
                })
                ->where([['x.object_id', $id], ['x.is_primary', 1]])
                ->limit(1)
                ->get();
            if (!empty($representationsRepo) && isset($representationsRepo[0])) {
                if (function_exists('gzuncompress')) {
                    $ps_uncompressed_data = @gzuncompress($representationsRepo[0]->media);
                    $media = (unserialize($ps_uncompressed_data));
                } else {
                    $media = unserialize(base64_decode($representationsRepo[0]->media));
                }
                $image = media_url($media, 'medium');
            }
            $result = array(
                'id' => $id,
                'image' => $image,
                'uk_title' => $titleUk,
                'en_title' => $titleEn,
            );
            return $result;
        }else{
            return false;
        }
    }

    public function find_list_item_label($item, $locale = 15, $field = 'name_singular'){
        $label = DB::table('ca_list_item_labels')->where([['locale_id', $locale], ['item_id', $item]])->get()->toArray();
        return isset($label[0]) && isset($label[0]->{$field}) ? $label[0]->{$field} : false;
    }

    public function find_entity_label_by_object($id, $locale = 15){
        $label = DB::table('ca_entity_labels as l')
                ->join('ca_objects_x_entities as x', 'l.entity_id', '=', 'x.entity_id')
                ->where([['x.object_id', $id], ['x.type_id', 108], ['l.locale_id', $locale]])
                ->get()->toArray();
        return isset($label[0]) && isset($label[0]->displayname) ? $label[0]->displayname : false;
    }

    public function get_all_list_items($lang = 15){
        $types = array(51, 52, 53, 54, 55, 56, 57, 58, 59);
        $labels = DB::table('ca_list_item_labels as l')
            ->join('ca_list_items as i', 'i.item_id', '=', 'l.item_id')
            ->whereIn('i.list_id', $types)
            ->where('l.locale_id', $lang)
            ->where('i.deleted', 0)
            ->where('i.is_enabled', 1)
            ->select('l.item_id', 'i.list_id', 'l.name_singular')
            ->orderBy('l.name_singular')
            ->get()->toArray();
        $results = array();
        if(!empty($labels)){
            foreach ($labels as $label){
                $results[$label->list_id] = isset($results[$label->list_id]) ? $results[$label->list_id] : array();
                array_push($results[$label->list_id], $label);
            }
        }
        return $results;
    }

    public function search_object(Request $request){
        $lang = $request->lang == 'en' ? 1 : 15;
        if(!empty($request->value)) {
            $searcher = new Searcher();
            $object_ids = $searcher->browseObjectSearch(array(), $request->value);
            if(!empty($object_ids) && count($object_ids)){
                $html = '';
                $repo = new ObjectRepository($object_ids, 23, true);
                $results = $repo->softLoad();
                foreach ($results as $result){
                    $author = $this->find_entity_label_by_object($result->id, $lang);
                    $title = !empty($author) ? $result->name . ' (' . $author . ')' : $result->name;
                    $main_name = $lang == 1 ? $this->get_name_by_lang($result->id) : $result->name;
                    $html .= '<a href="' . url($request->lang . "/object/" . $result->id . "-" . make_transliteration($main_name) . "/") . '">' . $title . '</a>';
                }
                return $html;
            }else{
                return $request->lang == 'uk' ? 'За даним запитом нічого не знайдено' : 'There no results by this key';
            }
        }
    }

    public function get_all_filters($all){
        $list = array();
        $authors = array();
        foreach ($all as $key => $item){
            $value = explode('-', $key);
            if($value[0] == 'msf'){
                if($item == 'on') {
                    if(!isset($list[$value[1]])){
                        $list[$value[1]] = array();
                    }
                    array_push($list[$value[1]], $value[2]);
                }
            }
            if($value[0] == 'author'){
                if($item == 'on') {
                    array_push($authors, $value[1]);
                }
            }
        }

		$results = array(
			'list' => $list,
			'authors' => $authors
		);
		if(isset($all['quick-search']) && !empty($all['quick-search'])){
			$results['quick-search'] = $all['quick-search'];
		}

        return $results;
    }

    public function get_all_years(){
        $results = DB::table('ca_attribute_values')
            ->where('value_longtext1', '>', 0)
            ->whereNotNull('value_longtext1')
            ->whereIn('element_id', array(129, 130))
            ->orderBy('value_longtext1', 'asc')
            ->pluck('value_longtext1');
        return $results;
    }

    public function get_entity_attributes($id){
        $results = DB::table('ca_attributes as a')
            ->join('ca_attribute_values as v', 'a.attribute_id', '=', 'v.attribute_id')
            ->where([['a.row_id', $id], ['a.table_num', 20]])
            ->select('v.element_id', 'a.locale_id', 'v.value_longtext1')->get()->toArray();
        return $results;
    }

    public function get_zoomify_src($src){
        return 'https://' . $_SERVER['SERVER_NAME'] . '/media/collectiveaccess/' . $src . '/';
    }

    public function get_name_by_lang($id, $lang = 15){
        $result = DB::table('ca_object_labels')->where([['object_id', $id], ['locale_id', $lang]])->select('name')->get()->toArray();
        return isset($result[0]) ? $result[0]->name : '';
    }

    public function show($slug){
        $find_list_item = array(
            'technique_attr',
            'medium_attr',
            'worktype_attr',
            'genre_attr',
            'iconclass_attr',
            'provenance_attr',
            'exposition_attr',
            'country_attr',
            'culture_attr'
        );

        $not_translated_items = array(
            151, 152
        );

        //розбити слаг і отримати ід (1 елемент масиву) та назву (решта елементів)
        $slug_array = explode('-', $slug);
        $id = $slug_array[0];
        unset($slug_array[0]);

        $lang = lang_code();
        $url = url()->full();

        $all['lang'] = $lang;
        $all['id'] = $id;
        $all['type'] = 'object';

        $ids[] = $id;

        $repo = new ObjectRepository($ids, 23, true);
        $results = $repo->all();
        $first = $results->first();

        if(empty($first)) return abort(404);

        //перевірка чи назва зі слагу підходить до цього об'єкту
        $main_name = $lang == 1 ? $this->get_name_by_lang($id) : $first->name;
        $object_name = make_transliteration($main_name);
        $slug_name = join('-', $slug_array);
//        $slug_name = make_transliteration(false, join(' ', $slug_array));
//        $slug_name = str_replace(array('є', 'я', 'ю', 'ц'), array('іе', 'іа', 'іу', 'тс'), $slug_name);
//        $main_name = str_replace(array('є', 'я', 'ю', 'ц'), array('іе', 'іа', 'іу', 'тс'), mb_strtolower($main_name));
//        $object_name = str_replace(array(',', '.', ':', '"', '\'', '(', ')', '+', '-', '—', 'ь', '’', '?', '!', '«', '»', '/'), array(''), $main_name);
//        $object_name = rtrim(str_replace('  ', ' ', $object_name), '-');
//        $object_name = rtrim(str_replace('  ', ' ', $object_name), ' ');
        //dd($slug_name . ' - ' . $object_name);
        if(count($slug_array) == 0){
            return redirect($url . '-' . make_transliteration($main_name));
        }
        if($slug_name != $object_name){
            return abort(404);
        }

        $first->dimension_label = false;
        $first->dimension_values = false;
        if(isset($first->dimensions) && count($first->dimensions)){
            $first->dimension_label = array();
            $first->dimension_values = array();
            if(isset($first->dimensions[0]) && !empty($first->dimensions[0])){
                array_push($first->dimension_label, trans('translations.short_height'));
                array_push($first->dimension_values, '<span itemprop="height" itemscope itemtype="https://schema.org/Distance">' . round($first->dimensions[0], 2) . '</span>');
            }
            if(isset($first->dimensions[1]) && !empty($first->dimensions[1])){
                array_push($first->dimension_label, trans('translations.short_width'));
                array_push($first->dimension_values, '<span itemprop="width" itemscope itemtype="https://schema.org/Distance">' . round($first->dimensions[1], 2) . '</span>');
            }
            if(isset($first->dimensions[2]) && !empty($first->dimensions[2])){
                array_push($first->dimension_label, trans('translations.short_depth'));
                array_push($first->dimension_values, '<span>' . round($first->dimensions[2], 2) . '</span>');
            }
            if(isset($first->dimensions[3]) && !empty($first->dimensions[3])){
                array_push($first->dimension_label, trans('translations.short_diameter'));
                array_push($first->dimension_values, '<span>' . round($first->dimensions[3], 2) . '</span>');
            }
            $first->dimension_label = join(' x ', $first->dimension_label);
            $first->dimension_values = join(' x ', $first->dimension_values);
        }

        foreach ($find_list_item as $list_item){
            if(isset($first->{$list_item})){
                $list_key = str_replace('_attr', '', $list_item);
                if(!isset($first->{$list_key})){
                    $first->{$list_key} = array();
                }
                foreach($first->{$list_item} as $item){
                    $first->{$list_key}[$item] = $this->find_list_item_label($item, $lang);
                }
            }
        }

        //сформувати тайли, якщо ще нема
        //$this->make_zoomify($id);

        $zoomify = array();
        //якщо є головне зображення тоді перевіримо чи для нього є сформований zoomify
        if(isset($first->representations)){
            foreach($first->representations as $representation){
                $db_item = DB::table('sg_zoomify_images')->where('id', $representation->id)->whereNotNull('src')->get()->toArray();
                if(!empty($db_item)){
                    //і запишемо в масив дані (в підмасив main для основного зображення)
                    $zoomify['main'] = array(
                        'id' => $db_item[0]->id,
                        'src' => $this->get_zoomify_src($db_item[0]->src),
                        'image' => media_url($representation->media, 'page'),
                        'width' => $db_item[0]->width,
                        'height' => $db_item[0]->height
                    );
                }
            }
        }
        //витягує всі зображення приєднані до об'єкта окрім основного
        $gallery = $repo->get_gallery_for_photo($id);
        if($gallery){
            $zoomify['gallery'] = array();
            foreach ($gallery as $gallery_item){
                $db_item = DB::table('sg_zoomify_images')->where('id', $gallery_item['id'])->get()->toArray();
                if(!empty($db_item)){
                    array_push(
                        $zoomify['gallery'],
                        array(
                            'id' => $db_item[0]->id,
                            'src' => $this->get_zoomify_src($db_item[0]->src),
                            'image' => $gallery_item['image'],
                            'width' => $db_item[0]->width,
                            'height' => $db_item[0]->height
                        )
                    );
                }
            }
        }

        $author = false;
        $author_info = array();
        if(isset($first->entities)){
            foreach($first->entities as $entity){
                if($entity->type_id == 108){
                    $author = $entity->name;
                    $author_repo = $this->get_entity_attributes($entity->id);
                    if(!empty($author_repo)){
                        foreach ($author_repo as $author_repo_item){
                            if(!isset($author_info[$author_repo_item->element_id]) && $author_repo_item->locale_id == $lang){
                                if($author_repo_item->element_id == 131){
                                    $author_info[$author_repo_item->element_id] = $this->find_list_item_label($author_repo_item->value_longtext1, $lang);
                                }else{
                                    $author_info[$author_repo_item->element_id] = $author_repo_item->value_longtext1;
                                }
                            }
                        }
                    }
                    $author_info[$entity->id] = array();
                }
            }
        }

        $related_ids = $repo->get_related_objects($id, 23);
        if(!empty($related_ids) && count($related_ids)) {
            $related_ids = array_column($related_ids, 'object_id');
            $related_repo = new ObjectRepository($related_ids, 23, true);
            $related = $related_repo->softLoad();
            foreach ($related as $item){
                $image = $repo->getPhoto($item->id);
                $item->author = $this->find_entity_label_by_object($item->id, $lang);
                $item->image = $image;
                $item->url = $item->id . '-' . make_transliteration($this->get_name_by_lang($item->id, 15));
            }
        }else{
            $related = false;
        }

        $seo = array();
        $seo_title = !empty($author) ? $first->name . ', ' . $author : $first->name;
        $seo['title'] = $seo_title . ' | ' . trans('translations.after_title');
        $seo['og_title'] = $seo['title'];
        if(isset($first->work_description)){
            $seo_description_array = explode(' ', mb_substr(strip_tags($first->work_description), 0, 200));
            $seo_description = join(' ', array_slice($seo_description_array, 0, count($seo_description_array) - 1));
        }else{
            $seo_description = $seo_title;
        }
        $seo['description'] = $seo_description . ' | ' . trans('translations.after_description');
        $seo['og_description'] = $seo['description'];
        if(!empty($first->representations)){
            foreach ($first->representations as $representation){
                $seo['image'] = media_url($representation->media, 'large');
            }
        }

        return view('objects.show',
            array(
                'result' => $first,
                'gallery' => $gallery,
                'zoomify' => $zoomify,
                'author_info' => $author_info,
                'related' => $related,
                'seo' => $seo,
                'lang' => $lang,
                'url' => $url
            )
        );
    }

    public function index(PhotoRequest $request){
        $lang = lang_code();

        $filters = $this->get_all_list_items($lang);
        $url = $request->fullUrl();
        $filter_years = $this->get_all_years();

        $all = $request->all();
        $all['lang'] = lang_code();

        $params = $this->get_all_filters($all);
        $years = isset($request->years) && $filter_years[0] . ',' . $filter_years[count($filter_years) - 1] != $request->years ? $request->years : false;
        $params['years'] = $years;
        $current_years = !empty($years) ? explode(',', $years) : false;
        $params['author'] = isset($request->author) ? $request->author : false;
        $filters_json = isset($params['list']) && count($params['list']) ? json_encode($params['list']) : '';

        $searcher = new Searcher();
        $search_string = !empty($request->{'quick-search'}) ? $request->{'quick-search'} : false;

        $query_uri = str_replace(array('uk?', 'en?'), '', basename($_SERVER['REQUEST_URI']));
        $query_string = '';
        if(!in_array($query_uri, array('uk', 'en'))){
            $query_uri = explode('&', $query_uri);
            foreach($query_uri as $query_item){
                $query_item_explode = explode('=', $query_item);
                if(!in_array($query_item_explode[0], array('page', 'uk', 'en'))){
                    if(isset($query_item_explode[1]) && !empty($query_item_explode[1])){
                        $query_string .= '&' . $query_item;
                    }
                }
            }
        }
        $query_string = ltrim($query_string, '&');
        $slug = '/';

        $md = md5(json_encode($params));
        $date = Carbon::now()->subMinutes(60);
        if($md == '9840418a504733252dc4253b670eb3a9') {
            DB::table('ca_ids')->where('created_at', '<=', $date)->delete();
            $object_ids = DB::table('ca_ids')->where('md', $md)->pluck('list')->toArray();
            if (!empty($object_ids)) {
                $object_ids = array_map('intval', explode(',', $object_ids[0]));
            }else{
                $object_ids = $searcher->browseObjectSearch($params, $search_string);
                shuffle($object_ids);
                DB::table('ca_ids')
                    ->updateOrInsert(
                        ['md' => $md],
                        ['md' => $md, 'list' => join(',', $object_ids)]
                    );
            }
        }else{
            $object_ids = $searcher->browseObjectSearch($params, $search_string);
        }

        $count = count($object_ids);
        $current_page = $request->currentPage();
        $pages = ceil((count($object_ids) / 15));
        $pages ?: $pages = 1;
        $start = $current_page * 15 - 15;

        $results = false;
        $page_ids = array_slice($object_ids, $start, 15);
        if (count($page_ids)) {
            $repo = new ObjectRepository($page_ids, 23, true);
            $results = $repo->softLoad();
            foreach ($results as $item){
                $image = $repo->getPhoto($item->id);
                $item->author = $this->find_entity_label_by_object($item->id, $lang);
                $item->image = $image;
                $item->url = $item->id . '-' . make_transliteration($this->get_name_by_lang($item->id, 15));
            }
        }

        $authors =  DB::table('ca_entity_labels')->where('locale_id', $lang)->orderBy('displayname')->get()->toArray();

        $seo = array();
        $seo['title'] = trans('translations.collection') . ' | ' . trans('translations.after_title');
        $seo['og_title'] = trans('translations.site_name') . ' - ' . $seo['title'];
        $seo['description'] = trans('translations.collection') . ' | ' . trans('translations.after_description');
        $seo['og_description'] = trans('translations.site_name') . ' - ' . $seo['description'];
        $lang_name = $lang == 1 ? 'en' : 'uk';
        $seo['image'] = 'https://collection-lvivgallery.org.ua/img/lvivgallery-archive-' . $lang_name . '.jpg';

        return view('objects.index', compact('results',
            'pages', 'current_page', 'query_string', 'slug', 'count',
            'filters', 'filter_years', 'params', 'url', 'search_string', 'current_years', 'filters_json',
            'authors',
            'seo')
        );
    }

    public function get_more_objects(PhotoRequest $request){
        $page = $_REQUEST['page'] + 1;
        $count = $_REQUEST['count'];
        $lang = $_REQUEST['lang'];
        $searcher = new Searcher();

        $all = array(
            'lang' => $request->lang
        );
        $params = $this->get_all_filters($all);
        if(isset($request->query_list) && !empty($request->query_list)){
            $params['list'] = json_decode($request->query_list, true);
        }
        if(isset($request->query_years) && !empty($request->query_years)){
            $params['years'] = $request->query_years;
        }
        if(isset($request->query_author) && !empty($request->query_author)){
            $params['authors'] = explode(',', $request->query_author);
        }
        $string = isset($request->query_string) && !empty($request->query_string) ? $request->query_string : false;

        $md = md5(json_encode($params));
        $date = Carbon::now()->subMinutes(60);
        if($md == 'dbebf208d741fed5fe7a7c107b1d1cc9') {
            $md = '9840418a504733252dc4253b670eb3a9';
            DB::table('ca_ids')->where('created_at', '<=', $date)->delete();
            $object_ids = DB::table('ca_ids')->where('md', $md)->pluck('list')->toArray();
            if (!empty($object_ids)) {
                $object_ids = array_map('intval', explode(',', $object_ids[0]));
            } else {
                $object_ids = $searcher->browseObjectSearch($params, $string);
                shuffle($object_ids);
                DB::table('ca_ids')
                    ->updateOrInsert(
                        ['md' => $md],
                        ['md' => $md, 'list' => join(',', $object_ids)]
                    );
            }
        }else{
            $object_ids = $searcher->browseObjectSearch($params, $string);
        }

        $count = count($object_ids);
        $current_page = $request->page + 1;
        $pages = ceil((count($object_ids) / 15));
        $pages ?: $pages = 1;
        $start = $current_page * 15 - 15;

        $results = false;
        $page_ids = array_slice($object_ids, $start, 15);

        if(count($page_ids)) {
            $repo = new ObjectRepository($page_ids, 23, true);
            $results = $repo->softLoad();
            foreach ($results as $item){
                $image = $repo->getPhoto($item->id);
                $item->author = $this->find_entity_label_by_object($item->id, $lang == 'uk' ? 15 : 1);
                $item->image = $image;
            }
        }

        $objects = array();
        if(!empty($results)) {
            foreach ($results as $result) {
                $object_item = array();
                $object_item['id'] = $result->id;
                $object_item['image'] = $result->image;
                $object_item['name'] = $result->name;
                $object_item['url'] =  make_transliteration($this->get_name_by_lang($result->id, 15));
                $object_item['author'] = $result->author;
                array_push($objects, $object_item);
            }
        }

        return json_encode($objects);
    }

    public function make_zoomify($id){
        $ids[] = $id;
        $representations_repo = DB::table('ca_object_representations as r')
            ->join('ca_objects_x_object_representations as x', 'x.representation_id', '=', 'r.representation_id')
            ->where('x.object_id', $id)
            ->get();

        if(!empty($representations_repo) && count($representations_repo) > 0){

            $domain = $_SERVER['HTTP_HOST'];
            $real_patch = explode($domain, __FILE__);
            $real_patch = $real_patch[0];

            foreach ($representations_repo as $representation){
                if (function_exists('gzuncompress')) {
                    $ps_uncompressed_data = @gzuncompress($representation->media);
                    $media = (unserialize($ps_uncompressed_data));
                } else {
                    $media = unserialize(base64_decode($representation->media));
                }

                $db_item = DB::table('sg_zoomify_images')->where('id', $representation->representation_id)->get()->toArray();

                if(empty($db_item)) {
                    $image = media_url($media, 'original');
                    $image_repo = explode('/', $image);
                    $image_name = $image_repo[count($image_repo) - 1];
                    unset($image_repo[count($image_repo) - 1]);
                    $image_url = join('/', $image_repo);
                    $image_patch = $real_patch . str_replace(array('https://', 'http://'), '', $image_url) . '/';

                    $zoomify = new ZoomifyFileProcessor();
                    $trimmed_filename = pathinfo($image_name, PATHINFO_FILENAME);
                    if(!file_exists($image_patch . $trimmed_filename)){
                        if ($zoomify->isSupportImageType($image_patch . $image_name)) {
                            $zoomify->ZoomifyProcess($image_patch . $image_name);
                            if(isset($zoomify->_v_saveToLocation))
                            DB::table('sg_zoomify_images')->insert(
                                array(
                                    'id' => $representation->representation_id,
                                    'src' => $zoomify->_v_saveToLocation,
                                    'image' => $image_patch . $image_name,
                                    'width' => isset($zoomify->originalWidth) ? $zoomify->originalWidth : 6000,
                                    'height' => isset($zoomify->originalHeight) ? $zoomify->originalHeight : 7500
                                )
                            );
                        }
                    }
                }
            }
        }
    }

    public function sitemap(){
        $objects = DB::table('ca_objects as o')
            ->join('ca_object_labels as l', 'o.object_id', '=', 'l.object_id')
            ->get()->toArray();
        $results = array();
        foreach ($objects as $object){
            array_push(
                $results,
                array(
                    'id' => $object->object_id,
                    'name' => $object->name,
                    'lang' => $object->locale_id == 1 ? 'en' : 'uk',
                    'url' => make_transliteration($this->get_name_by_lang($object->object_id, 15))
                )
            );
        }

        return response()->view('sitemap', [
            'objects' => $results,
        ])->header('Content-Type', 'text/xml');
    }

    public function test(){
//        $this->make_zoomify(40);
        return view('test');
    }
}
