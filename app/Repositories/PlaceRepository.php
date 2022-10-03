<?php
/**
 * Created by PhpStorm.
 * User: vlabutin
 * Date: 03.10.18
 * Time: 14:29
 */

namespace App\Repositories;


use Illuminate\Support\Facades\DB;

class PlaceRepository extends BaseRepository
{
	protected $objSingular = 'place';
	protected $objPlural = 'places';

	protected function occurrences()
	{
		return null;
	}

	protected function collections()
	{
		return null;
	}

	protected function objects()
	{
		$objects = DB::table('ca_objects_x_places as pivot')
			->whereIn('pivot.place_id', $this->ids)
			->leftJoin('ca_objects as o', function($join){
				$join->on('pivot.object_id', '=', 'o.object_id');
			})->leftJoin('ca_object_labels as ol', function($join){
				$join->on('o.object_id', 'ol.object_id')->where('ol.locale_id', $this->primaryLocale);
			})
			->select('pivot.relation_id','o.object_id as id', 'o.idno', 'ol.name as name', 'pivot.place_id as object_id')->get();

		return $this->objects = $objects;
	}

	protected function places()
	{
		return null;
	}

	protected function entities()
	{
		return null;
	}
}