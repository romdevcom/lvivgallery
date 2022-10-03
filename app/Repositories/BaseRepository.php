<?php
/**
 * Created by PhpStorm.
 * User: vlabutin
 * Date: 03.10.18
 * Time: 10:35
 */

namespace App\Repositories;


use Illuminate\Support\Facades\DB;

abstract class BaseRepository
{
	public $ids;
	public $attributes;

	protected $objSingular;
	protected $objPlural;
	protected $labelColName = 'name';

	public $objectsMain; // головна колекція обєктів

	public $collections; // залежні елементи
	public $entities;
	public $places;
	public $occurrences;
	public $objects;

	protected $lists = ['technique', 'techique_list', 'colorType', 'movie_technique','movie_genre']; // Список кодів списків, що мають бути включені у результат : ['technique', 'techique_list', 'colorType', 'movie_technique']
	protected $related = ['collections', 'representations', 'entities', 'places', 'occurrences', 'objects']; // Список пов'язаних сутностей у форматі : ['collections', 'representations', 'entities', 'places', 'occurrences']
	protected $occurrencesTypes = [196]; // Список type_id occurrences : [196]

	protected $multylang = ['object_dimensions', 'embed3d', 'stocknumber'];

	protected $primaryLocale; //локалізація сессії
	protected $media; // розміри медіа для використання (беруться із конфігу);
	protected $objectType;
	protected $force;
	protected $objectTableNum;

	public $softCalled = false;
	public $attributesCalled = false;


	public function __construct(array $ids, $type = null, $force = false)
	{
		$this->ids = $ids;
		$this->force = $force;
		$this->objectType = $type;
		$this->media = config('custom.media_sizes.all');
		$this->primaryLocale = lang_code();

		//TODO Перевірити чи встановленні $objSingular & $objPlural в разі невстановлення викинути ексепшн;

		$this->objectTableNum = config('custom.table_num_mapping.' . $this->objPlural);
	}

	public function softLoad(){
		if (!$this->softCalled) {
			$query = DB::table('ca_' . $this->objPlural . ' AS o')
                ->whereIn('o.object_id', $this->ids)
				->leftJoin('ca_' . $this->objSingular . '_labels as ol', function ($join)  {
					$join->on('o.' . $this->objSingular . '_id', '=', 'ol.' . $this->objSingular . '_id')
						->where('ol.locale_id', '=', $this->primaryLocale);
				});
                if($this->objectType) {
                    $query->where('o.type_id', '=', $this->objectType);
                }
                $query->where('o.deleted', 0);
				$query->select(
					'o.idno',
					'o.' . $this->objSingular . '_id as id',
					'o.type_id as object_type_id',
					'ol.' . $this->labelColName .' as name'
				);

			$objectsMain = $query->get();

			$this->softCalled = true;

			if(!count($this->ids)) {
				foreach ($objectsMain as $obj) {
					array_push($this->ids, $obj->id);
				}
			}

			$this->objectsMain = $objectsMain;
		}

		return $this->objectsMain;
	}

	public function withAttributes()
	{
		$this->softCalled ?: $this->softLoad();

		if(!$this->attributesCalled) {

			$this->setAttributes();

            $is_array = array(
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

            $not_languages = array(
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

			foreach ($this->objectsMain as &$object) {
				$values = $this->attributes->where('object_id', $object->id);
				foreach ($values as $value) {
//                    $lang = in_array($value->element_code, $not_languages) ? 15 : $this->primaryLocale;
                    $lang = in_array($value->element_code, $not_languages);
                    if($value->element_code == 'object_dimensions'){
                        if(!isset($object->dimensions)){
                            $object->dimensions = array();
                        }
                        array_push($object->dimensions, $value->value_longtext1);
                    }
					if($lang || in_array($value->element_code, $this->multylang) || $value->locale_id == $this->primaryLocale){
                        if(in_array($value->element_code, $is_array)) {
                            if($value->value_longtext1) {
                                if (!isset($object->{$value->element_code})) {
                                    $object->{$value->element_code} = array(in_array($value->element_code, $this->lists) ? $value->name_singular : $value->value_longtext1);
                                } else {
                                    array_push(
                                        $object->{$value->element_code},
                                        in_array($value->element_code, $this->lists) ? $value->name_singular : $value->value_longtext1
                                    );
                                }
                            }
                        }else{
                            if (isset($object->{$value->element_code})) {
                                if ($value->value_longtext1) {
                                    if (in_array($value->element_code, $this->lists)) {
                                        $object->{$value->element_code} = $value->name_singular;
                                    } else {
                                        $object->{$value->element_code} = $value->value_longtext1;
                                    }
                                } else {
                                    continue;
                                }
                            } else {
                                if (in_array($value->element_code, $this->lists)) {
                                    $object->{$value->element_code} = $value->name_singular;
                                } else {
                                    $object->{$value->element_code} = $value->value_longtext1;
                                }
                            }
                        }
					}
				}
			}
		}
		return $this->objectsMain;
	}

	//TODO Забрати з цього класу!
	public function formAttrVocabulary()
	{
		$terms = DB::table('ca_attributes as a')
			->where('table_num', $this->objectTableNum)
			->join('ca_metadata_element_labels as el', 'a.element_id', '=', 'el.element_id')
			->join('ca_metadata_elements as e', 'a.element_id', '=', 'e.element_id')
			->distinct()
			->select('el.name', 'e.element_code', 'el.locale_id as object_locale')
			->get();

		$vocabulary = [];

		foreach ($terms as $term) {
			if (isset($vocabulary[$term->element_code])) {
				if ($term->object_locale == $this->primaryLocale) {
					$term->name ? $vocabulary[$term->element_code] = $term->name : '';
				} else {
					$vocabulary[$term->element_code] ?: $vocabulary[$term->element_code] = $term->name;
				}
			} else {
				$vocabulary[$term->element_code] = $term->name;
			}
		}

		return $vocabulary;
	}

	public function with(array $relations = null)
	{
		$relations ?: $relations = $this->related;

		foreach ($relations as $relation) {
			if(method_exists($this, $relation)) {
				$this->{$relation}();
			}
		}

		foreach ($this->objectsMain as &$object) {
			foreach ($relations as $rel_obj) {

				if(!isset($this->{$rel_obj}) || !$this->{$rel_obj}) continue;

				if ($rel_obj == 'occurrences') {
					$this->mergeOccurrences($object);
				}

				$values = $this->{$rel_obj}->where('object_id', $object->id);
				foreach ($values as $value) {
					//TODO рефакторінг!

					if (isset($value->object_locale)) {
						if (isset($object->{$rel_obj}[$value->relation_id])) {
							if ($value->object_locale == $this->primaryLocale) {
								$value->name ? $object->{$rel_obj}[$value->relation_id]->name = $value->name : '';
								$value->relation_type ? $object->{$rel_obj}[$value->relation_id]->relation_type = $value->relation_type : '';
							} else {
								$object->{$rel_obj}[$value->relation_id]->name ?: $object->{$rel_obj}[$value->relation_id]->name = $value->name;
								$object->{$rel_obj}[$value->relation_id]->relation_type ?: $object->{$rel_obj}[$value->relation_id]->relation_type = $value->relation_type;
							}
						} else {
							$object->{$rel_obj}[$value->relation_id] = $value;
						}

					} else {
						$object->{$rel_obj}[$value->relation_id] = $value;
					}
				}
			}
		}

		return $this->objectsMain;
	}

	public function all()
	{
		$this->softCalled ?: $this->softLoad();
		$this->attributesCalled ?: $this->withAttributes();
		$this->with();

		return $this->objectsMain;
	}

	protected function mergeOccurrences(&$object)
	{
		foreach ($this->occurrences as $key => $type) {
			$values = $type->where('object_id', $object->id);
			foreach ($values as $value) {
				if (isset($object->{$key}[$value->id])) {
					if ($value->object_locale == $this->primaryLocale) {
						$value->name ? $object->{$key}[$value->id]->name = $value->name : '';
					} else {
						$object->{$key}[$value->id]->name ?: $object->{$key}[$value->id]->name = $value->name;
					}
				} else {
					$object->{$key}[$value->id] = $value;
				}
			}
		}
	}

	protected function setAttributes()
	{
		$attributes = DB::table('ca_attributes as a')
			->leftJoin('ca_metadata_elements as me', function ($join) {
				$join->on('a.element_id', '=', 'me.element_id');
			})
			->join('ca_attribute_values as av', function ($join) {
				$join->on('a.attribute_id', '=', 'av.attribute_id');
			})
			->leftJoin('ca_list_items as li', function ($join) {
				$join->on('av.item_id', '=', 'li.item_id');
			})
			->leftJoin('ca_list_item_labels as lil', function ($join) {
				$join->on('li.item_id', '=', 'lil.item_id')->where('lil.locale_id', $this->primaryLocale);
			})
			->select('me.element_code', 'me.element_id', 'a.locale_id', 'av.value_longtext1', 'a.row_id as object_id', 'lil.name_plural', 'lil.name_singular')
			->where([['a.table_num', $this->objectTableNum]])
			->whereIn('a.row_id', $this->ids)->get();

		return $this->attributes = $attributes;
	}

	abstract protected function occurrences();

	abstract protected function collections();

	abstract protected function objects();

	abstract protected function places();

	abstract protected function entities();
}
