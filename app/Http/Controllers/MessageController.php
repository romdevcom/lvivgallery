<?php

namespace App\Http\Controllers;

use App\Http\Requests\VideoRequest;

class MessageController extends ObjectController
{
	public function index(VideoRequest $request){
		$lang = lang_code();
		$url = $lang == 1 ? 'https://www.lvivcenter.org/umanews.json?lang=en' : 'https://www.lvivcenter.org/umanews.json';
		$lang = $lang == 1 ? '_en' : '';

		$json = file_get_contents($url);
		$results = json_decode($json,true);

		$queryString = $request->getQueryWithoutPage();
		$currentPage = $request->currentPage();
		$pages = ceil((count($results) / 12));
		$pages ?: $pages = 1;
		$start = $currentPage * 12 - 12;

		$results = array_slice($results, $start, 12);

		$slug = 'messages';

		$seo = array();
		$seo['title'] = trans('translations.messages').' | '.trans('translations.og_title');
		$seo['og_title'] = $seo['title'];
		$seo['description'] = trans('translations.messages').' | '.trans('translations.after_description');
		$seo['og_description'] = $seo['description'];

		return view('messages.index',	compact('results', 'pages', 'queryString', 'currentPage', 'slug', 'lang', 'seo'));
	}

	public function show($slug){
		$langCode = lang_code();
		$url = $langCode == 1 ? 'https://www.lvivcenter.org/umanews.json?lang=en' : 'https://www.lvivcenter.org/umanews.json';
		$lang = $langCode == 1 ? '_en' : '';

		$json = file_get_contents($url);
		$results = json_decode($json,true);

		$message = array_search($slug, array_column($results, 'slug'));

		if($message === 0 || $message){
			$message = $results[$message];
		}else{
			return abort(404);
		}

		if(!empty($message)){
			$messageDate = explode('-',$message['date']);
			$message['date'] = $messageDate[2].' '.date_ua($langCode, $messageDate[1]).' '.$messageDate[0];

			if(isset($message['description'])) {
				$message['description'] = str_replace(array('<div><br></div>', '<div>', '</div>'), array('', '<p>', '</p>'), $message['description']);
			}elseif(isset($message['description'.$lang])){
				$message['description'.$lang] = str_replace(array('<div><br></div>', '<div>', '</div>'), array('', '<p>', '</p>'), $message['description'.$lang]);
			}
		}
		$related = isset($message) ? array_slice($results, 0, 5) : false;

		$seo = array();
		$seo['title'] = $message['title'.$lang].' | '.trans('translations.og_title');
		$seo['og_title'] = $seo['title'];
		$seo['description'] = trans('translations.messages').' '.$message['title'.$lang].' | '.trans('translations.after_description');
		$seo['og_description'] = $seo['description'];

		return view('messages.show', ['message' => $message, 'lang' => $lang, 'related' => $related, 'seo' => $seo]);
	}
}
