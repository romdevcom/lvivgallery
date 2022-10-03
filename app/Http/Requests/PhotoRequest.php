<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhotoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    public function getQueryWithoutPage()
	{
		$queryString = $this->getQueryString();

		$queryArray = explode('&', $queryString);

		foreach ($queryArray as $key => $queryParam) {
			if (str_contains($queryParam, 'page')) {
				unset($queryArray[$key]);
				$queryString = implode('&', $queryArray);
			}
		}

		return $queryString;
	}

	public function formParams()
	{
		$all = $this->all();
		$this->flash();

		$params['type'] = 'photo';
		$params['attributes'] = [];

		if (isset($all['dates']) && $all['dates']) {
		    $dates = str_replace(',','-', $all['dates']);
			$params['attributes']['dates_from_to']['value'] = $dates;
			$params['attributes']['dates_from_to']['type'] = 'integer';
		}

		$params['attributes']['lists'] = [];

		if (isset($all['techniques'])) {
			$params['attributes']['lists']['technique'] = $all['techniques'];
		}

		$params['relations'] = [];

		if (isset($all['collections'])) {
			$params['relations']['collections']['values'] = $all['collections'];
			$params['relations']['collections']['singular'] = 'collection';
			$params['relations']['collections']['plural'] = 'collections';
		}

		if (isset($all['places'])) {
			$params['relations']['places']['values'] = $all['places'];
			$params['relations']['places']['singular'] = 'place';
			$params['relations']['places']['plural'] = 'places';
		}

		return $params;
	}

	public function currentPage()
	{
		return $this->only('page') ? $this->only('page')['page'] : 1;
	}
}
