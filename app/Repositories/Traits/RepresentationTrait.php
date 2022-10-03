<?php
/**
 * Created by PhpStorm.
 * User: vlabutin
 * Date: 03.10.18
 * Time: 11:21
 */

namespace App\Repositories\Traits;


trait RepresentationTrait
{
	public $representations;

	public function decodeRepresentation()
	{
		foreach ($this->representations as &$representation) {
			$representation->media = $this->formMediaArray($representation->media);
			$representation->media_metadata = $this->formMetadata($representation->media_metadata);
		}

		return $this->representations;
	}

	public function decode($value)
	{
		if (function_exists('gzuncompress')) {
			$ps_uncompressed_data = @gzuncompress($value);
			return (unserialize($ps_uncompressed_data));
		} else {
			return unserialize(base64_decode($value));
		}
	}

	public function formMediaArray($value)
	{
		$array = $this->decode($value);

		foreach ($array as $key => $value) {
			if (!in_array($key, $this->media) && $key != 'tilepic' && $key != 'full') {
//			if (!in_array($key, $this->media)) {
				unset($array[$key]);
			}
		}

		return $array;
	}

	public function formMetadata($value)
	{
		return $this->decode($value);
	}
}