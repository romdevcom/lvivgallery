<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CollectionRequest extends FormRequest
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
		$params = array();
		if(!empty($all['full-search'])){
			$params['full-search'] = $all['full-search'];
		}
		return $params;
	}

	public function currentPage()
	{
		return $this->only('page') ? $this->only('page')['page'] : 1;
	}
}
