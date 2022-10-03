<?php
/**
 * Created by PhpStorm.
 * User: vlabutin
 * Date: 03.10.18
 * Time: 11:54
 */

namespace App\Repositories;


use App\Repositories\Contracts\RepresentationTraitContract;
use App\Repositories\Traits\RepresentationTrait;
use App\Searcher;
use Illuminate\Support\Facades\DB;

class ObjectRepository extends BaseRepository implements RepresentationTraitContract
{
	use RepresentationTrait;

	protected $objSingular = 'object';
	protected $objPlural = 'objects';
	protected $objectType = 23;

	protected function occurrences()
	{
		$occurrences = DB::table('ca_objects_x_occurrences as pivot')
			->whereIn('pivot.object_id', $this->ids)
			->leftJoin('ca_occurrences as o', function ($join) {
				$join->on('pivot.occurrence_id', '=', 'o.occurrence_id')->whereIn('o.type_id', $this->occurrencesTypes);
			})
			->leftJoin('ca_list_items as li', function ($join) {
				$join->on('o.type_id', '=', 'li.item_id');
			})
			->leftJoin('ca_occurrence_labels as ol', function ($join) {
				$join->on('o.occurrence_id', '=', 'ol.occurrence_id')/*->where('ol.locale_id', $this->primaryLocale)*/
				;
			})
			->select('pivot.occurrence_id as id', 'pivot.object_id', 'ol.locale_id as object_locale', 'li.idno as category', 'ol.name')
			->get()->groupBy('category');

		return $this->occurrences = $occurrences;
	}

	protected function collections()
	{
		$collections = DB::table('ca_objects_x_collections as pivot')
			->whereIn('pivot.object_id', $this->ids)
			->leftJoin('ca_collections as  c', function ($join) {
				$join->on('pivot.collection_id', '=', 'c.collection_id');
			})
			->leftJoin('ca_collection_labels as cl', function ($join) {
				$join->on('c.collection_id', '=', 'cl.collection_id');
			})
			->leftJoin('ca_relationship_type_labels as rl', function ($join) {
				$join->on('pivot.type_id', '=', 'rl.type_id')->on('rl.locale_id', '=', 'cl.locale_id');
			})
			->select('pivot.relation_id', 'c.collection_id as id', 'cl.locale_id as object_locale', 'pivot.object_id', 'cl.name', 'rl.typename as relation_type')
			->get();

		return $this->collections = $collections;
	}

	protected function objects()
	{
		return null;
	}

	protected function places($lang = false)
	{
		$places = DB::table('ca_objects_x_places as pivot')
			->whereIn('pivot.object_id', $this->ids)
			->leftJoin('ca_place_labels as pl', function ($join) {
				$join->on('pivot.place_id', '=', 'pl.place_id')/*->where('pl.locale_id', '=', 1)*/
				;
			})
			->leftJoin('ca_relationship_type_labels as rl', function ($join) {
				$join->on('pivot.type_id', '=', 'rl.type_id')->on('rl.locale_id', '=', 'pl.locale_id');
			})
			->select('pivot.relation_id','pivot.place_id as id', 'pl.locale_id as object_locale',
                'pivot.object_id', 'pl.name', 'rl.typename as relation_type')
			->distinct()
			->get();

		return $this->places = $places;
	}

	protected function entities()
	{
		$entities = DB::table('ca_objects_x_entities as pivot')
			->whereIn('pivot.object_id', $this->ids)
			->join('ca_entity_labels as el', function ($join) {
				$join->on('pivot.entity_id', '=', 'el.entity_id');
			})
			->leftJoin('ca_relationship_type_labels as rl', function ($join) {
				$join->on('pivot.type_id', '=', 'rl.type_id')->on('rl.locale_id', 'el.locale_id');
			})
			->select(DB::raw('pivot.relation_id, pivot.entity_id as id, el.locale_id as object_locale, 
			pivot.object_id, el.displayname as name, rl.typename as relation_type, pivot.type_id'))
			->get();

		return $this->entities = $entities;
	}

	public function representations($id = false)
	{
		$ids = $id ? array($id) : $this->ids;
		$representations = DB::table('ca_objects_x_object_representations as pivot')
			->whereIn('pivot.object_id', $ids)
			->where('is_primary',1)
			->leftJoin('ca_object_representations as r', function ($join) {
				$join->on('pivot.representation_id', '=', 'r.representation_id');
			})
			->select('pivot.relation_id', 'r.representation_id as id', 'pivot.object_id as object_id', 'r.original_filename', 'r.media', 'r.media_metadata')
			->limit(1)
			->get();

		$this->representations = $representations;
		$this->decodeRepresentation();
		return $this->representations;
	}

	public function relatedForSingle($type = 27)
	{
	    $searcher = new Searcher();
		$res = $searcher->getRelatedForPhoto($this->ids[0], 23, $type);
		$objects = array();
		if(!empty($res)) {
			$repo = new ObjectRepository($res);
			$objects = $repo->all();
			foreach ($objects as $object) {
				$object->representations = $repo->representations($object->id)[0];
			}
		}
        return count($objects) ? $objects : [];
	}

    public function get_related_objects($id, $type, $limit = 15){
        $objects = DB::table('ca_objects as o')->select('o.object_id')
            ->join('ca_objects_x_object_representations as or', 'o.object_id', 'or.object_id')
            ->where('o.deleted', '!=', '1')
            ->where('o.type_id', '=', $type)
            ->whereNotIn('o.object_id', array($id))
            ->inRandomOrder()
            ->limit($limit)->get()
            ->toArray();
        return $objects;
    }

    public function relatedForSingleVideo()
    {
        $searcher = new Searcher();
        $repo = new ObjectRepository($searcher->getRelatedForVideo($this->ids[0], 4));
        return count($repo->ids) ? $repo->all() : [];
    }

	public function getObjectIds($type = 27, $limit = 1000000)
    {
		$objects = DB::table('ca_objects')->select('object_id')
			->where('deleted','!=','1')
			->where('type_id','=',$type)
			->limit($limit)->get();
		return $objects;
	}

	public function getObjectIdsByCollection($type = 27, $collection = 1)
	{
		$objects = DB::table('ca_objects as o')->select('o.object_id')
			->leftJoin('ca_objects_x_collections as  c', function ($join) {
				$join->on('c.object_id', '=', 'o.object_id');
			})->join('ca_objects_x_object_representations as or', 'o.object_id', 'or.object_id')
			->where('o.deleted','!=','1')
			->where('o.type_id','=',$type)
			->join('ca_attributes as st_a', 'o.object_id', 'st_a.row_id')
			->join('ca_attribute_values as st_av', 'st_av.attribute_id', 'st_a.attribute_id')
			->where('st_av.value_longtext1',250)
			->where('c.collection_id','=',$collection)
			->get();
		return $objects;
	}

	public function getObjectCount($type = 27)
    {
		$count = DB::table('ca_objects as o')
			->where('o.deleted','!=','1')
			->where('o.type_id','=',$type)
			->join('ca_attributes as st_a', 'o.object_id', 'st_a.row_id')
			->join('ca_attribute_values as st_av', 'st_av.attribute_id', 'st_a.attribute_id')
			->where('st_av.value_longtext1',250)
			->count();
		return $count;
	}

	public function getCollectionsCount()
	{
		$count = DB::table('ca_collections')
			->where('deleted','!=','1')->count();
		return $count;
	}

	public function getLastObjects($limit = 10)
    {

		$lastIds = DB::table('ca_objects as o')
			->select('o.object_id','o.type_id', 'l.log_datetime')
			->join('ca_attributes as st_a', 'o.object_id', 'st_a.row_id')
			->join('ca_attribute_values as st_av', 'st_av.attribute_id', 'st_a.attribute_id')
			->join('ca_change_log as l', 'o.object_id', 'l.logged_row_id')
//            ->join('ca_attributes as a_date', 'o.object_id', 'a_date.row_id')
//            ->join('ca_attribute_values as av_date', 'a_date.attribute_id', 'av_date.attribute_id')
			->where([['st_av.value_longtext1',250],['st_a.element_id',120],['o.deleted','!=','1']])
			->orderBy('l.log_datetime', 'DESC')
			->orderBy('l.log_id', 'DESC')
//			->limit($limit)->get();
			->limit($limit)->pluck('object_id')->toArray();
//		dd($lastIds);

//		$this->ids = $lastIds;

		return $lastIds;
	}

	public function getPhoto($id, $size = 'medium'){
		$objects = DB::table('ca_object_representations as r')
			->leftJoin('ca_objects_x_object_representations as x', function ($join) {
				$join->on('x.representation_id', '=', 'r.representation_id');
			})
			->where([['x.object_id', $id], ['x.is_primary', 1]])
			->limit(1)
			->get();

		if (function_exists('gzuncompress')) {
			$ps_uncompressed_data = @gzuncompress($objects[0]->media);
			$media = (unserialize($ps_uncompressed_data));
		} else {
			$media = unserialize(base64_decode($objects[0]->media));
		}
//		dd(media_url($media,$size));
		return media_url($media,$size);
	}

	public function getTitleTranslation($id, $lang){
		return  DB::table('ca_object_labels')
			->where([['object_id',$id],['locale_id',$lang]])
			->pluck('name')->toArray();
	}

	public function getAttributes($langOn = false, $lang = 1, $attr = false, $ids = false){
		$ids = !empty($ids) ? $ids : $this->ids;
		$query = DB::table('ca_attributes as a')
			->join('ca_attribute_values as v', 'v.attribute_id', 'a.attribute_id')
			->whereIn('a.row_id', $ids);
		if($langOn){
			$query->where('a.locale_id', $lang);
		}
		if($attr) {
			$query->whereIn('a.element_id', $attr);
		}

		$attributes = $query->select('a.row_id', 'a.element_id', 'v.value_longtext1')->get();
		return $attributes;
	}

    public function get_list_label($id, $lang = 1){
        $query = DB::table('ca_list_item_labels as l')->where([['l.locale_id', $lang], ['l.item_id', $id]]);
        $label = $query->select('l.name_singular')->get();
        return !empty($label) ? $label[0]->name_singular : false;
    }

	public function getPlaces($lang = 1,$ids = false){
		$ids = !empty($ids) ? $ids : $this->ids;
		$places = DB::table('ca_objects_x_places as p')
			->whereIn('p.object_id', $ids)
			->join('ca_place_labels as pl', 'p.place_id', 'pl.place_id')
			->where('pl.locale_id', '=', $lang)
			->leftJoin('ca_relationship_type_labels as rl', function ($join) {
				$join->on('p.type_id', '=', 'rl.type_id')->on('rl.locale_id', '=', 'pl.locale_id');
			})
			->select('p.object_id', 'pl.name')
			->get();
		return $places;
	}

	public function objectsByAttribute($attribute, $data){
		$ids = DB::table('ca_objects as o')
			->join('ca_attributes as a','o.object_id','a.row_id')
			->join('ca_attribute_values as v','v.attribute_id','a.attribute_id')
			->where('o.deleted','!=','1')
			->join('ca_attributes as st_a', 'o.object_id', 'st_a.row_id')
			->join('ca_attribute_values as st_av', 'st_av.attribute_id', 'st_a.attribute_id')
			->where('st_av.value_longtext1',250)
			->where('v.value_longtext1',$data)
			->where('a.element_id',$attribute)
			->inRandomOrder()
			->pluck('o.object_id')->toArray();
		return $ids;
	}

    public function get_gallery_for_photo($id){
        if(!empty($id)){
            $representationsRepo = DB::table('ca_object_representations as r')
                ->leftJoin('ca_objects_x_object_representations as x', function ($join) {
                    $join->on('x.representation_id', '=', 'r.representation_id');
                })
                ->where([['x.object_id', $id], ['x.is_primary', 0]])
                ->get();
            if(!empty($representationsRepo) && count($representationsRepo) > 0){
                $gallery = array();
                foreach ($representationsRepo as $representation){
                    if (function_exists('gzuncompress')) {
                        $ps_uncompressed_data = @gzuncompress($representation->media);
                        $media = (unserialize($ps_uncompressed_data));
                    } else {
                        $media = unserialize(base64_decode($representation->media));
                    }
                    $image = media_url($media, 'page');
                    $tile = media_url($media, 'tilepic');
                    if(!empty($image))
                        array_push(
                            $gallery,
                            array(
                                'id' => $representation->representation_id,
                                'image' => $image,
                                'tile' => $tile,
                                'tilepic' => $media['tilepic']
                            )
                        );
                }
                return $gallery;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
