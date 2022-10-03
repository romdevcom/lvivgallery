<?php
/**
 * Created by PhpStorm.
 * User: vlabutin
 * Date: 25.09.18
 * Time: 19:04
 */

namespace App\Repositories;


use Illuminate\Support\Facades\DB;

class EntityRepositoryOld
{
	public $ids = [];
	public $entities;
	public $objects;

	protected $attributes;
	protected $lists = [];

	public function __construct(array $ids = null)
	{
		if(count($ids)) {
			$this->ids = $ids;
			$this->entities = DB::table('ca_entities as e')
				->whereIn('e.entity_id', $ids)
				->leftJoin('ca_entity_labels as el', function($join){
					$join->on('e.entity_id' , '=', 'el.entity_id')->where('el.locale_id', 15);
				})
				->select('e.entity_id as id', 'el.displayname as name')
				->get();
		} else {
			$this->ids = $ids;
			$this->entities = DB::table('ca_entities as e')
				->leftJoin('ca_entity_labels as el', function($join){
					$join->on('e.entity_id' , '=', 'el.entity_id')->where('locale_id', 15);
				})
				->select('e.entity_id as id', 'el.displayname as name')
				->get();
		}

	}

	protected function setAttributes()
	{
		$attributes = DB::table('ca_attributes as a')
			->leftJoin('ca_metadata_elements as me', function ($join) {
				$join->on('a.element_id', '=', 'me.element_id');
			})
			->rightJoin('ca_attribute_values as av', function ($join) {
				$join->on('a.attribute_id', '=', 'av.attribute_id');
			})
			->leftJoin('ca_list_items as li', function ($join) {
				$join->on('av.item_id', '=', 'li.item_id');
			})
			->leftJoin('ca_list_item_labels as lil', function ($join) {
				$join->on('li.item_id', '=', 'lil.item_id')->where('lil.locale_id', 15);
			})
			->select('me.element_code', 'me.element_id', 'av.value_longtext1', 'a.row_id as object_id', 'lil.name_plural', 'lil.name_singular')
			->where([['a.table_num', 20], ['a.locale_id', 1]])
			->whereIn('a.row_id', $this->ids)->get();

		return $this->attributes = $attributes;
	}

	public function softLoad()
	{
		$this->setAttributes();

		foreach ($this->entities as &$photo) {

			$values = $this->attributes->where('object_id', $photo->id);
			foreach ($values as $value) {
				if (in_array($value->element_code, $this->lists)) {
					$photo->{$value->element_code}['singular'] = $value->name_singular;
					$photo->{$value->element_code}['plural'] = $value->name_plural;
				} else {
					$photo->{$value->element_code} = $value->value_longtext1;
				}
			}
		}

		$this->objects();

		return $this->entities;
	}

	public function objects()
	{
		$objects = DB::table('ca_objects_x_entities as pivot')
			->whereIn('pivot.entity_id', $this->ids)
			->leftJoin('ca_objects as o', function($join){
				$join->on('pivot.object_id', '=', 'o.object_id');
			})
			->pluck('o.object_id')->unique()->toArray();

		$objRepo = new ObjectRepository($objects);
		return $this->entities->objects = $objRepo->with(['representations']);
	}

}