<?php


use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Repositories\ObjectRepository;

function lang_code()
{
	return config('custom.localemapping.' . LaravelLocalization::getCurrentLocale());
}

function get_my_ip(){
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function remove_get_parameter($url, $par){
    $array = explode('?', $url);
    $result = count($array) > 1 ? $array[0] . '?' : $array[0];
    if(isset($array[1])){
        $params = explode('&', $array[1]);
        foreach($params as $param){
            $item = explode('=', $param);
            if($item[0] != $par){
                $result .= join('=', $item) . '&';
            }
        }
    }
    return rtrim($result, '&');
}

function make_transliteration($string_cyr = false, $string_lat = false){
    $string = '';
    $cyr = array(
        'щ', 'ж', 'х', 'є', 'ц', 'ч', 'ш', 'ю', 'я', 'й', 'ї', 'а', 'б', 'в', 'г', 'ґ', 'д', 'е', 'з', 'и', 'і', 'к',
        'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'ий', 'ь');
    $lat = array(
        'shch', 'zh', 'kh', 'ie', 'ts', 'ch', 'sh', 'iu', 'ia', 'iy', 'ij', 'a', 'b', 'v', 'h', 'g', 'd', 'e', 'z', 'y', 'i', 'k',
        'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'ии', '');
    if(!empty($string_cyr) || !empty($string_lat)){
        if(!empty($string_cyr)){
			$string = mb_strtolower($string_cyr);
//            $string = str_replace(array(',', '.', ':', '"', '\'', '(', ')', '+', '-', '—', '–', '’', '?', '!', '«', '»', '/'), array(''), mb_strtolower($string_cyr));
            $string = str_replace(' ', '-', $string);
            $string = rtrim(str_replace($cyr, $lat, $string), '-');
			$string = preg_replace('/[^a-z0-9 \-]/ui', '', $string);
	        $string = str_replace('--', '-', $string);
	        $string = str_replace('--', '-', $string);
        }else{
            $string = str_replace($lat, $cyr, $string_lat);
        }
    }
    return rtrim($string, '-');
}

function media_url($params, $size)
{
	$array = isset($params[$size]) ? $params[$size] : false;
	return $array && array_key_exists('VOLUME',$array) && array_key_exists('HASH',$array) ? url(
		'https://collection-lvivgallery.org.ua/media/collectiveaccess/' .
		$array['VOLUME'] . '/' .
		$array['HASH'] . '/' .
		$array['MAGIC'] . '_' .
		$array['FILENAME']) : '';
}

function split_text($text, $size = 150){
	$result = false;
	if($size < strlen($text)){
		$result = mb_strcut($text,0, $size).'...';
	}
	return $result;
}

function message_date($date){
	$split = explode('-',$date);
	$format = $split[2][0] == '0' ? str_replace('0','',$split[2]) : $split[2];
	$format .= $split[1][0] == '0' ? '.' . str_replace('0','',$split[1]) : '.' . $split[1];
	$format .= '.' . $split[0];
	return $format;
}

function simplify_date($date){
    $split = explode('T',$date);
    return $split[0];
}

function endsWith($string, $endString){
	$len = strlen($endString);
	if ($len == 0) {
		return true;
	}
	return (substr($string, -$len) === $endString);
}

function sanitizeQueryString($string, $type = false)
{
	$queryString = str_replace('%20&', '&', $string);
	if (endsWith($queryString, '%20'))
		$queryString = substr($queryString, 0, -3);
	$queryString = json_encode($queryString, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	if ($queryString == '""'){
		$queryString = '';
	}else{
		$queryString = rtrim($queryString,'"');
		$queryString = ltrim($queryString,'"');
	}
	if($type == 'collection'){
		$queryString = '&'.$queryString;
	}
	return $queryString;
}

function getAllFilters($all){
	$videoFilters = '';
	$photoFilters = '';
	$collectionsFilters = '';
	$interviewFilters = '';
	$mapFilters = '';

	$paramsVideo = array();
	$paramsVideo['type'] = 'movie';
	$paramsPhoto = array();
	$paramsPhoto['type'] = 'photo';
	$paramsInterview = array();
	$paramsInterview['type'] = 'interview';
    $paramsMap = array();
    $paramsMap['type'] = 'map';

	$objInCollections = false;

	if(isset($all['collection_type']) && !empty($all['collection_type'])){
		$collectionType = !empty($all['collection_type']) ? $all['collection_type'] : false;
		$collectionType = is_array($collectionType) ? array_shift($collectionType) : false;
		if(is_array($collectionType)){
			foreach ($collectionType as $item){
				$collectionsFilters .= 'collection_type['.$item.']='.$item.'&';
				$videoFilters .= 'collection_type['.$item.']='.$item.'&';
				$photoFilters .= 'collection_type['.$item.']='.$item.'&';
                $mapFilters .= 'collection_type['.$item.']='.$item.'&';
			}
		}else{
			$collectionsFilters .= 'collection_type['.$collectionType.']='.$collectionType.'&';
			$videoFilters .= 'collection_type['.$collectionType.']='.$collectionType.'&';
			$photoFilters .= 'collection_type['.$collectionType.']='.$collectionType.'&';
            $mapFilters .= 'collection_type['.$collectionType.']='.$collectionType.'&';
		}
	}
	if(isset($all['collections']) && !empty($all['collections'])){
		$objInCollections = true;
		$paramsPhoto['relations'] = array();
		$paramsPhoto['relations']['collections'] = array();
		$paramsPhoto['relations']['collections']['values'] = array();
		$paramsPhoto['relations']['collections']['singular'] = 'collection';
		$paramsPhoto['relations']['collections']['plural'] = 'collections';
		$paramsVideo['relations'] = array();
		$paramsVideo['relations']['collections'] = array();
		$paramsVideo['relations']['collections']['values'] = array();
		$paramsVideo['relations']['collections']['singular'] = 'collection';
		$paramsVideo['relations']['collections']['plural'] = 'collections';
        $paramsMap['relations'] = array();
        $paramsMap['relations']['collections'] = array();
        $paramsMap['relations']['collections']['values'] = array();
        $paramsMap['relations']['collections']['singular'] = 'collection';
        $paramsMap['relations']['collections']['plural'] = 'collections';
		$paramsInterview['relations'] = array();
		$paramsInterview['relations']['collections'] = array();
		$paramsInterview['relations']['collections']['values'] = array();
		$paramsInterview['relations']['collections']['singular'] = 'collection';
		$paramsInterview['relations']['collections']['plural'] = 'collections';
		foreach ($all['collections'] as $key => $collection){
			$paramsPhoto['relations']['collections']['values'][$key] = $collection;
            $paramsMap['relations']['collections']['values'][$key] = $collection;
			$paramsVideo['relations']['collections']['values'][$key] = $collection;
			$paramsInterview['relations']['collections']['values'][$key] = $collection;
			$videoFilters .= 'collections['.$key.']='.$collection.'&';
			$photoFilters .= 'collections['.$key.']='.$collection.'&';
			$mapFilters .= 'collections['.$key.']='.$collection.'&';
			$collectionsFilters .= 'collections['.$key.']='.$collection.'&';
			$interviewFilters .= 'collections['.$key.']='.$collection.'&';
		}
	}
    if(isset($all['entities']) && !empty($all['entities'])){
        $objInCollections = true;
        $paramsPhoto['relations'] = isset($paramsPhoto['relations']) ? $paramsPhoto['relations'] : array();
        $paramsPhoto['relations']['entities'] = array();
        $paramsPhoto['relations']['entities']['values'] = array();
        $paramsPhoto['relations']['entities']['singular'] = 'entity';
        $paramsPhoto['relations']['entities']['plural'] = 'entities';
        $paramsVideo['relations'] = isset($paramsVideo['relations']) ? $paramsVideo['relations'] : array();
        $paramsVideo['relations']['entities'] = array();
        $paramsVideo['relations']['entities']['values'] = array();
        $paramsVideo['relations']['entities']['singular'] = 'entity';
        $paramsVideo['relations']['entities']['plural'] = 'entities';
        $paramsMap['relations'] = isset($paramsMap['relations']) ? $paramsMap['relations'] : array();
        $paramsMap['relations']['entities'] = array();
        $paramsMap['relations']['entities']['values'] = array();
        $paramsMap['relations']['entities']['singular'] = 'entity';
        $paramsMap['relations']['entities']['plural'] = 'entities';
        $paramsInterview['relations'] = isset($paramsInterview['relations']) ? $paramsInterview['relations'] : array();
        $paramsInterview['relations']['entities'] = array();
        $paramsInterview['relations']['entities']['values'] = array();
        $paramsInterview['relations']['entities']['singular'] = 'entity';
        $paramsInterview['relations']['entities']['plural'] = 'entities';
        foreach ($all['entities'] as $key => $entity){
            $paramsPhoto['relations']['entities']['values'][$key] = $entity;
            $paramsMap['relations']['entities']['values'][$key] = $entity;
            $paramsVideo['relations']['entities']['values'][$key] = $entity;
            $paramsInterview['relations']['entities']['values'][$key] = $entity;
            $videoFilters .= 'entities['.$key.']='.$entity.'&';
            $photoFilters .= 'entities['.$key.']='.$entity.'&';
            $mapFilters .= 'entities['.$key.']='.$entity.'&';
            $collectionsFilters .= 'entities['.$key.']='.$entity.'&';
            $interviewFilters .= 'entities['.$key.']='.$entity.'&';
        }
    }
	if(isset($all['dates']) && $all['dates'] != '1600,'.date('Y')){
		$paramsPhoto['attributes'] = array();
		$paramsPhoto['attributes']['dates_from_to'] = array();
		$paramsPhoto['attributes']['dates_from_to']['value'] = $all['dates'];
		$paramsPhoto['attributes']['dates_from_to']['type'] = 'integer';
        $paramsMap['attributes'] = array();
        $paramsMap['attributes']['dates_from_to'] = array();
        $paramsMap['attributes']['dates_from_to']['value'] = $all['dates'];
        $paramsMap['attributes']['dates_from_to']['type'] = 'integer';
		$paramsVideo['attributes'] = array();
		$paramsVideo['attributes']['dates_from_to'] = array();
		$paramsVideo['attributes']['dates_from_to']['value'] = $all['dates'];
		$paramsVideo['attributes']['dates_from_to']['type'] = 'integer';
		$videoFilters .= 'dates='.$all['dates'].'&';
		$photoFilters .= 'dates='.$all['dates'].'&';
		$mapFilters .= 'dates='.$all['dates'].'&';
		$collectionsFilters .= 'dates='.$all['dates'].'&';
        $interviewFilters .= 'dates='.$all['dates'].'&';
	}
	if(isset($all['sort'])){
		$videoFilters .= 'sort=id&';
		$photoFilters .= 'sort=id&';
		$mapFilters .= 'sort=id&';
		$collectionsFilters .= 'sort=id&';
		$interviewFilters .= 'sort=id&';
	}
	if(isset($all['full-search'])){
		$videoFilters .= 'full-search='.$all['full-search'].'&';
		$photoFilters .= 'full-search='.$all['full-search'].'&';
		$mapFilters .= 'full-search='.$all['full-search'].'&';
		$collectionsFilters .= 'full-search='.$all['full-search'].'&';
		$interviewFilters .= 'full-search='.$all['full-search'].'&';
	}
	if(isset($all['places']) && !empty($all['places'])){
		$objInCollections = true;
		if(!isset($paramsPhoto['relations'])) {
			$paramsPhoto['relations'] = array();
		}
		$paramsPhoto['relations']['places'] = array();
		$paramsPhoto['relations']['places']['values'] = array();
		$paramsPhoto['relations']['places']['singular'] = 'place';
		$paramsPhoto['relations']['places']['plural'] = 'places';
		if(!isset($paramsMap['relations'])) {
            $paramsMap['relations'] = array();
		}
        $paramsMap['relations']['places'] = array();
        $paramsMap['relations']['places']['values'] = array();
        $paramsMap['relations']['places']['singular'] = 'place';
        $paramsMap['relations']['places']['plural'] = 'places';
		if(!isset($paramsVideo['relations'])) {
			$paramsVideo['relations'] = array();
		}
		$paramsVideo['relations']['places'] = array();
		$paramsVideo['relations']['places']['values'] = array();
		$paramsVideo['relations']['places']['singular'] = 'place';
		$paramsVideo['relations']['places']['plural'] = 'places';
		foreach ($all['places'] as $key => $place){
			$videoFilters .= 'places['.$key.']='.$place.'&';
			$photoFilters .= 'places['.$key.']='.$place.'&';
			$mapFilters .= 'places['.$key.']='.$place.'&';
			$collectionsFilters .= 'places['.$key.']='.$place.'&';
            $interviewFilters .= 'places['.$key.']='.$place.'&';
			$paramsPhoto['relations']['places']['values'][$key] = $place;
			$paramsVideo['relations']['places']['values'][$key] = $place;
            $paramsMap['relations']['places']['values'][$key] = $place;
		}
	}
	if(isset($all['techniques']) && !empty($all['techniques'])){
		$objInCollections = true;
		if(!isset($paramsPhoto['attributes'])) {
			$paramsPhoto['attributes'] = array();
		}
		$paramsPhoto['attributes']['lists'] = array();
		$paramsPhoto['attributes']['lists']['technique'] = array();
		foreach ($all['techniques'] as $key => $technique){
			$videoFilters .= 'techniques['.$key.']='.$technique.'&';
			$photoFilters .= 'techniques['.$key.']='.$technique.'&';
			$mapFilters .= 'techniques['.$key.']='.$technique.'&';
			$collectionsFilters .= 'techniques['.$key.']='.$technique.'&';
			$paramsPhoto['attributes']['lists']['technique'][$key] = $technique;
		}
	}
	if(isset($all['movie_genre']) && !empty($all['movie_genre'])){
		$objInCollections = true;
		if(!isset($paramsVideo['attributes'])) {
			$paramsVideo['attributes'] = array();
		}
		if(!isset($paramsVideo['attributes']['lists'])) {
			$paramsVideo['attributes']['lists'] = array();
		}
		$paramsVideo['attributes']['lists']['movie_genre'] = array();
		foreach ($all['movie_genre'] as $key => $genre){
			$videoFilters .= 'movie_genre['.$key.']='.$genre.'&';
			$photoFilters .= 'movie_genre['.$key.']='.$genre.'&';
			$mapFilters .= 'movie_genre['.$key.']='.$genre.'&';
			$collectionsFilters .= 'movie_genre['.$key.']='.$genre.'&';
			$paramsVideo['attributes']['lists']['movie_genre'][$key] = $genre;
		}
	}
	if(isset($all['movie_technique']) && !empty($all['movie_technique'])){
		$objInCollections = true;
		if(!isset($paramsVideo['attributes'])) {
			$paramsVideo['attributes'] = array();
		}
		if(!isset($paramsVideo['attributes']['lists'])) {
			$paramsVideo['attributes']['lists'] = array();
		}
		$paramsPhoto['attributes']['lists']['movie_technique'] = array();
		foreach ($all['movie_technique'] as $key => $technique){
			$videoFilters .= 'movie_technique['.$key.']='.$technique.'&';
			$photoFilters .= 'movie_technique['.$key.']='.$technique.'&';
			$mapFilters .= 'movie_technique['.$key.']='.$technique.'&';
			$collectionsFilters .= 'movie_technique['.$key.']='.$technique.'&';
			$paramsVideo['attributes']['lists']['movie_technique'][$key] = $technique;
		}
	}
	if(!isset($paramsPhoto['relations'])){
		$paramsPhoto['relations'] = array();
	}
	if(!isset($paramsMap['relations'])){
        $paramsMap['relations'] = array();
	}
	if(!isset($paramsInterview['relations'])){
		$paramsInterview['relations'] = array();
	}
	if(!isset($paramsVideo['relations'])){
		$paramsVideo['relations'] = array();
	}
	if(!isset($paramsPhoto['attributes'])){
		$paramsPhoto['attributes'] = array();
	}
	if(!isset($paramsMap['attributes'])){
        $paramsMap['attributes'] = array();
	}
	if(!isset($paramsInterview['attributes'])){
		$paramsInterview['attributes'] = array();
	}
	if(!isset($paramsVideo['attributes'])){
		$paramsVideo['attributes'] = array();
	}
	if(!isset($paramsPhoto['attributes']['lists'])){
		$paramsPhoto['attributes']['lists'] = array();
	}
	if(!isset($paramsMap['attributes']['lists'])){
        $paramsMap['attributes']['lists'] = array();
	}
	if(!isset($paramsInterview['attributes']['lists'])){
		$paramsInterview['attributes']['lists'] = array();
	}
	if(!isset($paramsVideo['attributes']['lists'])){
		$paramsVideo['attributes']['lists'] = array();
	}
	$videoFilters = !empty($videoFilters) ? '?'.rtrim(str_replace(array('&&'),array('&'),$videoFilters),'&') : '';
	$photoFilters = !empty($photoFilters) ? '?'.rtrim(str_replace('&&','&',$photoFilters),'&') : '';
	$mapFilters = !empty($mapFilters) ? '?'.rtrim(str_replace('&&','&',$mapFilters),'&') : '';
	$collectionsFilters = !empty($collectionsFilters) ? '?'.rtrim(str_replace(array('&&'),array('&'),$collectionsFilters),'&') : '';
	$interviewFilters = !empty($interviewFilters) ? '?'.rtrim(str_replace(array('&&'),array('&'),$interviewFilters),'&') : '';

	$result = array(
		'filters' => array(
			'collection' => $collectionsFilters,
			'photo' => $photoFilters,
			'map' => $mapFilters,
			'video' => $videoFilters,
			'interview' => $interviewFilters,
		),
		'params' => array(
			'photo' => $paramsPhoto,
			'map' => $paramsMap,
			'video' => $paramsVideo,
			'interview' => $paramsInterview
		),
		'use_collection' => $objInCollections
	);

	return $result;
}

function date_ua($lang, $month){
	$translation = array(
		'15' => array(
			'01' => 'січня',
			'02' => 'лютого',
			'03' => 'березня',
			'04' => 'квітня',
			'05' => 'травня',
			'06' => 'червня',
			'07' => 'липня',
			'08' => 'серпня',
			'09' => 'вересня',
			'10' => 'жовтня',
			'11' => 'листопада',
			'12' => 'грудня'
		),
		'1' => array(
			'01' => 'January',
			'02' => 'February',
			'03' => 'March',
			'04' => 'April',
			'05' => 'May',
			'06' => 'June',
			'07' => 'July',
			'08' => 'August',
			'09' => 'September',
			'10' => 'October',
			'11' => 'November',
			'12' => 'December'
		)
	);
	return $translation[$lang][$month];
}

function get_http_response_code($url) {
    $headers = get_headers($url);
    return substr($headers[0], 9, 3);
}