<?php
/**
 * Created by PhpStorm.
 * User: vlabutin
 * Date: 03.10.18
 * Time: 14:22
 */

namespace App\Repositories;


use App\Repositories\Contracts\RepresentationTraitContract;
use App\Repositories\Traits\RepresentationTrait;
use Illuminate\Support\Facades\DB;

class CollectionRepository extends BaseRepository implements RepresentationTraitContract
{
    use RepresentationTrait;

	protected $objSingular = 'collection';
	protected $objPlural = 'collections';
	protected $labelColName = 'name';

	public function __construct(array $ids, $type = null)
    {
        parent::__construct($ids, $type);
    }

    protected function occurrences()
	{
		return null;
	}

	protected function collections()
	{
		return null;
	}

	public function objects()
	{
		$objects = DB::table('ca_objects_x_collections as pivot')
			->whereIn('pivot.collection_id', $this->ids)
			->leftJoin('ca_objects as o', function($join){
				$join->on('pivot.object_id', '=', 'o.object_id');
			})->leftJoin('ca_object_labels as ol', function($join){
				$join->on('o.object_id', 'ol.object_id')->where('ol.locale_id', $this->primaryLocale);
			})
			->select('pivot.relation_id','o.object_id as id', 'o.idno', 'ol.name as name',
                'pivot.collection_id as object_id')->get();

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

	public function get_collections($type = false, $string = false, $order = 'name_sort', $importance = 117)
	{
		$firstIds = array();
		$queryFirst = DB::table('ca_collections as o')
			->join('ca_collection_labels as ol',
				'o.collection_id', '=',
				'ol.collection_id'
			)
			->join('ca_attributes as at',
				'o.collection_id', '=',
				'at.row_id',
				'left outer'
			)
			->join('ca_attribute_values as av',
				'at.attribute_id', '=',
				'av.attribute_id',
				'left outer'
			)
			->where([['deleted','!=','1'],['ol.locale_id', lang_code()],['at.element_id',$importance]])
			->where('at.element_id',$importance)
			->whereNotNull('av.value_longtext1')
			->orderBy('av.value_longtext1','desc')
			->orderBy('ol.'.$order);
		if($type){
//			$queryFirst->where('o.type_id','=',$type);
			$queryFirst->join('ca_attributes as af', 'o.collection_id', 'af.row_id')
				->join('ca_attribute_values as afv', 'af.attribute_id', 'afv.attribute_id')
				->where('afv.value_longtext1', $type);
		}
		if($string){
			$queryFirst->where('ol.name','LIKE','%' . $string . '%');
		}
		$objectsFirst = $queryFirst->select('o.collection_id as id', 'ol.*','av.value_longtext1')->get()->toArray();
		foreach ($objectsFirst as $o){
			array_push($firstIds, $o->collection_id);
		}

		$queryLast = DB::table('ca_collections as o')
			->join('ca_collection_labels as ol',
				'o.collection_id', '=',
				'ol.collection_id'
			)
			->where([['ol.locale_id', lang_code()],['deleted','!=','1']])
			->whereNotIn('o.collection_id', $firstIds)
			->orderBy('ol.'.$order);
		if($type){
//			$queryLast->where('o.type_id','=',$type);
			$queryLast->join('ca_attributes as af', 'o.collection_id', 'af.row_id')
				->join('ca_attribute_values as afv', 'af.attribute_id', 'afv.attribute_id')
				->where('afv.value_longtext1', $type);
		}
		if($string){
			$queryLast->where('ol.name','LIKE','%' . $string . '%');
		}
		$objectsLast = $queryLast->select('o.collection_id as id', 'ol.*')->get()->toArray();
		$objects = array_merge($objectsFirst,$objectsLast);
		return $objects;
	}

	public function get_full_search($string)
	{
		$objects = DB::table('ca_collection_labels')->select('collection_id')
            ->where('name','LIKE','%'.$string.'%')->get();

		return $objects;
	}

	public function representations()
    {
        $representations = DB::table('ca_object_representations_x_collections as pivot')
            ->whereIn('pivot.collection_id', $this->ids)
            ->leftJoin('ca_object_representations as r', function ($join) {
                $join->on('pivot.representation_id', '=', 'r.representation_id');
            })
            ->select('pivot.relation_id', 'r.representation_id as id', 'pivot.collection_id as collection_id',
                'r.original_filename', 'r.media', 'r.media_metadata')
            ->get();

        $this->representations = $representations;
        $this->decodeRepresentation();

        return $this->representations;
    }

	public function get_related_objects($id, $type = null){
		$objects = DB::table('ca_objects_x_collections as rel')
			->leftJoin('ca_objects as obj', function ($join) {
				$join->on('rel.object_id', '=', 'obj.object_id');
			})
			->where('rel.collection_id','=',$id)
			->where('obj.type_id','=',$type)
			->where('obj.deleted','!=','1')
			->select('rel.object_id')->get();
		return $objects;
	}

	public function get_object_count($type = 27){
		$count = DB::table('ca_objects as o')
			->where('o.deleted','!=','1')
			->where('o.type_id','=',$type)
			->join('ca_attributes as st_a', 'o.object_id', 'st_a.row_id')
			->join('ca_attribute_values as st_av', 'st_av.attribute_id', 'st_a.attribute_id')
			->where('st_av.value_longtext1',250)
			->count();
		return $count;
	}

	public function get_collections_by_type($type, $ids = false, $view = 'count'){
		$query = DB::table('ca_collections as o')
			->join('ca_attributes as af', 'o.collection_id', 'af.row_id')
			->join('ca_attribute_values as afv', 'af.attribute_id', 'afv.attribute_id')
			->where('afv.value_longtext1', $type)
			->where('o.deleted',0);
		if(!empty($ids) && count($ids) > 0) {
			$query->whereIn('collection_id',$ids);
		}
		switch ($view){
			case 'ids':
				$result = $query->pluck('collection_id')->toArray();
				break;
			default:
				$result = $query->count('collection_id');
		}
		return $result;
	}

	public function get_collection_image($id, $size = 'medium'){
		$objects = DB::table('ca_object_representations as r')
			->leftJoin('ca_object_representations_x_collections as x', function ($join) {
				$join->on('x.representation_id', '=', 'r.representation_id');
			})
			->where('x.collection_id',$id)
			->limit(1)
			->get();

		if (function_exists('gzuncompress')) {
			$ps_uncompressed_data = @gzuncompress($objects[0]->media);
			$media = (unserialize($ps_uncompressed_data));
		} else {
			$media = unserialize(base64_decode($objects[0]->media));
		}
		$image = media_url($media,$size);
		return !empty($image) ? $image : false;
	}
}