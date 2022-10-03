<?php
/**
 * Created by PhpStorm.
 * User: vlabutin
 * Date: 26.09.18
 * Time: 11:22
 */

namespace App;


use function foo\func;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Searcher
{
	public $searchString;

    protected $searchable = array(
        array(
            'plural' => 'objects',
            'singular' => 'object',
            'table_num' => 56,
        ),
        array(
            'plural' => 'collections',
            'singular' => 'collection',
        ),
    );

    public function browseObjectSearch(array $params, $string = false)
    {
        $query = DB::table('ca_objects as o')->where('o.type_id', 23);

        if(isset($params['list']) && count($params['list'])){
            foreach($params['list'] as $key => $list_item){
                $query->join('ca_attributes as list_attr_' . $key, 'o.object_id', '=', 'list_attr_' . $key . '.row_id')
                    ->join('ca_attribute_values as list_attr_val_' . $key, 'list_attr_' . $key . '.attribute_id', '=', 'list_attr_val_' . $key . '.attribute_id')
                    ->whereIn('list_attr_val_' . $key . '.item_id', $list_item)
                    ->where('list_attr_' . $key . '.table_num', 57);
            }
        }

        if(!empty($params['years'])){
            $years = explode(',', $params['years']);
            $query->join('ca_attributes as year_attr_start', 'o.object_id', '=', 'year_attr_start.row_id')
                ->join('ca_attribute_values as year_attr_start_val', 'year_attr_start.attribute_id', '=', 'year_attr_start_val.attribute_id')
                ->where([['year_attr_start_val.element_id', 129], ['year_attr_start_val.value_longtext1', '>=', $years[0]]]);
            $query->join('ca_attributes as year_attr_end', 'o.object_id', '=', 'year_attr_end.row_id')
                ->join('ca_attribute_values as year_attr_end_val', 'year_attr_end.attribute_id', '=', 'year_attr_end_val.attribute_id')
                ->where([['year_attr_end_val.element_id', 130], ['year_attr_end_val.value_longtext1', '<=', $years[1]]])
                ->where([['year_attr_start.table_num', 57],['year_attr_end.table_num', 57]]);
        }

        if(isset($params['authors']) && count($params['authors'])){
                $query->join('ca_objects_x_entities as author', 'o.object_id', '=', 'author.object_id')
                    ->whereIn('author.entity_id', $params['authors']);
        }
        if(!empty($params['author'])){
            $query->join('ca_objects_x_entities as ent_x', 'o.object_id', '=', 'ent_x.object_id')
                ->where([['ent_x.type_id', 108], ['ent_x.entity_id', $params['author']]]);
        }

        $query->join('ca_objects_x_object_representations as or', 'o.object_id', 'or.object_id');
        $query->orderBy('o.object_id','DESC');

        $res = $query->distinct()->pluck('o.object_id')->toArray();

        if(!empty($string)){
            $query_res = array();
            $queryTitle = DB::table('ca_objects as o')->where('o.type_id', 23)
                ->whereIn('o.object_id', $res)
                ->join('ca_object_labels as ol', 'ol.object_id', '=', 'o.object_id')
                ->where('ol.name', 'LIKE', '%' . $string . '%')
                ->orderBy('o.object_id', 'DESC');
            $query_title_res = $queryTitle->distinct()->pluck('o.object_id')->toArray();
            $query_attrs = DB::table('ca_objects as o')->where('o.type_id', 23)
                ->whereIn('o.object_id', $res)
                ->join('ca_attributes as atr', 'atr.row_id', 'o.object_id')
                ->join('ca_attribute_values as atrv', 'atr.attribute_id', '=', 'atrv.attribute_id')
                ->where([['atrv.value_longtext1', 'LIKE', '%' . $string . '%'],['atr.table_num', 57]])
                ->whereNotIn('o.object_id', $query_title_res)
                ->orderBy('o.object_id', 'DESC');
            $query_attrs_res = $query_attrs->distinct()->pluck('o.object_id')->toArray();
            $query_res = array_merge($query_title_res, $query_attrs_res);
            $query_entity = DB::table('ca_objects as o')->where('o.type_id', 23)
                ->whereIn('o.object_id', $res)
                ->join('ca_objects_x_entities as oxe', 'oxe.object_id', 'o.object_id')
                ->join('ca_entity_labels as el', 'oxe.entity_id', '=', 'el.entity_id')
                ->where('el.displayname', 'LIKE', '%' . $string . '%')
                ->whereNotIn('o.object_id', $query_res)
                ->orderBy('o.object_id', 'DESC');
            $query_entity_res = $query_entity->distinct()->pluck('o.object_id')->toArray();
            $query_res = array_merge($query_res, $query_entity_res);
            $res = $query_res;
        }

        return $res;
    }

    public function fullSearch($string)
    {
        $phrases = explode(' ', $string);
        $result = [];

        foreach ($this->searchable as $object) {

            $nameCol = $object['singular'] == 'entity' ? 'displayname' : 'name';


            $query = DB::table('ca_' . $object['plural'])
                ->join(
                    'ca_' . $object['singular'] . '_labels',
                    'ca_' . $object['plural'] . '.' . $object['singular'] . '_id', '=',
                    'ca_' . $object['singular'] . '_labels.' . $object['singular'] . '_id');
            $query->where('ca_' . $object['plural'] . '.idno', 'LIKE', '%' . $string . '%')
                ->orWhere('ca_' . $object['singular'] . '_labels.' . $nameCol, 'LIKE', '%' . $string . '%');

            if (isset($object['table_num'])) {
                $query->join(
                    'ca_attributes as a',
                    'a.row_id',
                    '=',
                    'ca_' . $object['plural'] . '.' . $object['singular'] . '_id'
                )
                    ->where('a.table_num', $object['table_num'])
                    ->join('ca_attribute_values as av', 'a.attribute_id', '=', 'av.attribute_id')
                    ->orWhere('av.value_longtext1', 'LIKE', '%' . $string . '%');
            }

            $query->select('ca_' . $object['plural'] . '.' . $object['singular'] . '_id as id',
                'ca_' . $object['plural'] . '.type_id as type');

            if($object['singular'] == 'object') {
            	$query->join('ca_objects_x_object_representations as or', 'ca_objects.object_id', 'or.object_id');
				$query->join('ca_attributes as st_a', 'ca_' . $object['plural'], 'st_a.row_id')
					->join('ca_attribute_values as st_av', 'st_av.attribute_id', 'st_a.attribute_id')
					->where('st_av.value_longtext1',250);
			}

            $result[$object['plural']] = $query->get()->unique('id');
        }

        return $result;
    }

    public function getRelatedForPhoto($id, $quantity, $type = 27)
    {
		$resOccur = DB::table('ca_objects as o')
			->join('ca_objects_x_collections as pc', 'o.object_id', 'pc.object_id')
			->join('ca_objects_x_collections as rc', 'pc.collection_id', 'rc.collection_id')
			->join('ca_objects as orl', 'orl.object_id', 'rc.object_id')
			->join('ca_objects_x_object_representations as or', 'orl.object_id', 'or.object_id')
			->where('o.object_id', $id)
			->join('ca_attributes as st_a', 'orl.object_id', 'st_a.row_id')
			->join('ca_attribute_values as st_av', 'st_av.attribute_id', 'st_a.attribute_id')
			->where('st_av.value_longtext1',250)
			->where('orl.type_id', $type)
			->distinct()->pluck('orl.object_id')->toArray();
		if(count($resOccur) > $quantity){
			$resIds = array_random($resOccur, $quantity);
		}else{
			$resIds = count($resOccur) == 0 ? array() : $resOccur;
		}
		if(count($resIds) < $quantity){
			$resCol = DB::table('ca_objects as o')
				->join('ca_objects_x_places as pp', 'o.object_id', 'pp.object_id')
				->join('ca_objects_x_places as rp', 'pp.place_id', 'rp.place_id')
				->join('ca_objects as orl', 'orl.object_id', 'rp.object_id')
				->join('ca_objects_x_object_representations as or', 'orl.object_id', 'or.object_id')
				->where('o.object_id', $id)
				->join('ca_attributes as st_a', 'o.object_id', 'st_a.row_id')
				->join('ca_attribute_values as st_av', 'st_av.attribute_id', 'st_a.attribute_id')
				->where('st_av.value_longtext1',250)
				->where('orl.type_id', $type)
				->distinct()->pluck('orl.object_id')->toArray();

			if(count($resCol) > $quantity) {
				$resCol = array_random($resCol, $quantity - count($resIds));
			}else{
				$resCol = count($resCol) == 0 ? array() : $resCol;
			}
			$resIds = array_merge($resCol, $resIds);

			if(count($resIds) < $quantity){
				$resPlaces = DB::table('ca_objects as o')
					->join('ca_objects_x_occurrences as pp', 'o.object_id', 'pp.object_id')
					->join('ca_objects_x_occurrences as rp', 'pp.occurrence_id', 'rp.occurrence_id')
					->join('ca_objects as orl', 'orl.object_id', 'rp.object_id')
					->join('ca_objects_x_object_representations as or', 'orl.object_id', 'or.object_id')
					->where('o.object_id', $id)
					->join('ca_attributes as st_a', 'o.object_id', 'st_a.row_id')
					->join('ca_attribute_values as st_av', 'st_av.attribute_id', 'st_a.attribute_id')
					->where('st_av.value_longtext1',250)
					->where('orl.type_id', $type)
					->distinct()->pluck('orl.object_id')->toArray();

				if(count($resPlaces) > $quantity && ($quantity - count($resIds)) > $quantity){
					$resIds = array_random($resPlaces, $quantity);
				}else{
					$resIds = count($resPlaces) == 0 ? array() : $resPlaces;
				}
				$resIds = array_merge($resPlaces, $resIds);
			}
		}

        $res = $resIds;
        $related = !empty($res) && count($res) > 0 ? DB::table('ca_objects as o')->whereIn('object_id', $res)
            ->distinct()->pluck('o.object_id')->toArray() : false;

        return $related;
    }

    public function getRelatedForVideo($id, $quantity)
    {

        $resCol = DB::table('ca_objects as o')
            ->join('ca_objects_x_collections as pc', 'o.object_id', 'pc.object_id')
            ->join('ca_objects_x_collections as rc', 'pc.collection_id', 'rc.collection_id')
            ->join('ca_objects as orl', 'orl.object_id', 'rc.object_id')
            ->join('ca_objects_x_object_representations as or', 'orl.object_id', 'or.object_id')
            ->where('o.object_id', $id)
            ->join('ca_attributes as st_a', 'o.object_id', 'st_a.row_id')
			->join('ca_attribute_values as st_av', 'st_av.attribute_id', 'st_a.attribute_id')
			->where('st_av.value_longtext1',250)
            //->where('orl.type_id', 25)
            ->distinct()->pluck('orl.object_id')->toArray();

        $resPlaces = DB::table('ca_objects as o')
            ->join('ca_objects_x_places as pp', 'o.object_id', 'pp.object_id')
            ->join('ca_objects_x_places as rp', 'pp.place_id', 'rp.place_id')
            ->join('ca_objects as orl', 'orl.object_id', 'rp.object_id')
            ->join('ca_objects_x_object_representations as or', 'orl.object_id', 'or.object_id')
            ->where('o.object_id', $id)
			->join('ca_attributes as st_a', 'orl.object_id', 'st_a.row_id')
			->join('ca_attribute_values as st_av', 'st_av.attribute_id', 'st_a.attribute_id')
			->where('st_av.value_longtext1',250)
            ->where('orl.type_id', 25)
            ->distinct()->pluck('orl.object_id')->toArray();

        $resIds = array_merge($resCol, $resPlaces);
        $res = count($resIds) > 4 ? array_random(array_unique($resIds), $quantity) : $resIds;

        $related = DB::table('ca_objects as o')
            ->whereIn('object_id', $res)
            ->distinct()->pluck('o.object_id')->toArray();;

        return $related;
    }

    public function prepareListSelect($listCode, $type = 27, $order = false, $collection = false, $ids = false)
    {
		$list = DB::table('ca_list_items as li')->where('li.idno', 'Root node for ' . $listCode)
			->join('ca_list_items as lir', 'li.item_id', 'lir.parent_id')
			->join('ca_list_item_labels as lirl', 'lir.item_id', 'lirl.item_id')
			->where('lirl.locale_id', lang_code())
			->select('lir.item_id', 'lirl.*');
		if(!empty($order)){
			$list = $list->orderBy('lirl.'.$order,'ASC');
		}
		$list = $list->get();
        $listIds = array();

        foreach ($list as $key => $elem){
            if(!in_array($elem->item_id,$listIds)) {
                array_push($listIds,$elem->item_id);
                $cnt = DB::table('ca_attribute_values as av')->where('av.item_id', $elem->item_id)
                    ->join('ca_attributes as a', 'av.attribute_id', 'a.attribute_id')
                    ->join('ca_objects as o', 'a.row_id', 'o.object_id')
                    ->join('ca_objects_x_object_representations as or', 'or.object_id', 'o.object_id')
                    ->where('o.deleted', 0)
                    ->join('ca_attributes as st_a', 'o.object_id', 'st_a.row_id')
                    ->join('ca_attribute_values as st_av', 'st_av.attribute_id', 'st_a.attribute_id')
                    ->where('st_av.value_longtext1', 250)
                    ->where('o.type_id', $type);
                if ($collection) {
                    $cnt = $cnt->join('ca_objects_x_collections as oc', 'oc.object_id', 'o.object_id')
                        ->where('oc.collection_id', $collection);
                }
                if ($ids) {
                    $cnt = $cnt->whereIn('o.object_id', $ids);
                }
                $cnt = $cnt->select('o.object_id')
                    ->distinct('o.object_id')->count('o.object_id');
                if ($cnt) {
                    $elem->cnt = $cnt;
                } else {
                    $list->forget($key);
                }
                $elem->name_singular = trans('translations.technique_'.$elem->item_id);
            }else{
                $list->forget($key);
            }
        }
        return $list;

    }

    public function prepareObjSelect($objId, $type = 27, $order = false, $importance = false, $collection = false, $ids = false)
    {
		if(!empty($order)){
			if(!empty($importance)){
				$objFirst = DB::table('ca_' . $objId['plural'] . ' as o')
					->join('ca_' . $objId['singular'] . '_labels as ol',
						'o.' . $objId['singular'] . '_id', '=',
						'ol.' . $objId['singular'] . '_id'
					)
					->join('ca_attributes as at',
						'o.' . $objId['singular'] . '_id', '=',
						'at.row_id',
						'left outer'
					)
					->join('ca_attribute_values as av',
						'at.attribute_id', '=',
						'av.attribute_id',
						'left outer'
					)
					->where([['ol.locale_id', lang_code()],['at.element_id',$importance]])
					->whereNotNull('av.value_longtext1')
					->select('o.' . $objId['singular'] . '_id as id', 'ol.*','av.value_longtext1')
					->orderBy('av.value_longtext1','desc')
					->orderBy('ol.'.$order);
				$objFirst = $objFirst->get()->toArray();
				$firstIds = array();
				foreach ($objFirst as $o){
					array_push($firstIds, $o->{$objId['singular'].'_id'});
				}
				$objLast = DB::table('ca_' . $objId['plural'] . ' as o')
					->join('ca_' . $objId['singular'] . '_labels as ol',
						'o.' . $objId['singular'] . '_id', '=',
						'ol.' . $objId['singular'] . '_id'
					)
					->where('ol.locale_id', lang_code())
					->whereNotIn('o.' . $objId['singular'] . '_id', $firstIds)
					->select('o.' . $objId['singular'] . '_id as id', 'ol.*')
					->orderBy('ol.'.$order);
				$objLast= $objLast->get()->toArray();
				$obj = array_merge($objFirst,$objLast);
			}else{
				$obj = DB::table('ca_' . $objId['plural'] . ' as o')
					->join('ca_' . $objId['singular'] . '_labels as ol',
						'o.' . $objId['singular'] . '_id', '=',
						'ol.' . $objId['singular'] . '_id'
					)
					->where('ol.locale_id', lang_code())
					->select('o.' . $objId['singular'] . '_id as id', 'ol.*')
					->orderBy('ol.'.$order);
				$obj = $obj->get()->toArray();
			}

		}else{
			$obj = DB::table('ca_' . $objId['plural'] . ' as o')
				->join('ca_' . $objId['singular'] . '_labels as ol',
					'o.' . $objId['singular'] . '_id', '=',
					'ol.' . $objId['singular'] . '_id'
				)
				->where('ol.locale_id', lang_code())

				->select('o.' . $objId['singular'] . '_id as id', 'ol.*');
			$obj = $obj->get()->toArray();
		}

		foreach ($obj as $key => $elem) {
			$cnt = DB::table('ca_objects_x_' . $objId['plural'] . ' as r')
				->join('ca_objects as o', 'o.object_id', 'r.object_id')
				->join('ca_objects_x_object_representations as or', 'o.object_id', 'or.object_id')
				->where('o.type_id', $type)
				->where('o.deleted', 0)
				->join('ca_attributes as st_a', 'o.object_id', 'st_a.row_id')
				->join('ca_attribute_values as st_av', 'st_av.attribute_id', 'st_a.attribute_id')
				->where('st_av.value_longtext1',250)
				->where('r.' . $objId['singular'] . '_id', $elem->{$objId['singular'] . '_id'});
			if($collection){
				$cnt->join('ca_objects_x_collections as oc', 'oc.object_id', 'o.object_id')
					->where('oc.collection_id', $collection);
			}
			if($ids){
				$cnt = $cnt->whereIn('o.object_id', $ids);
			}
			$cnt = $cnt->distinct('o.object_id')->count('o.object_id');
			if ($cnt) {
				$elem->cnt = $cnt;
			}
		}
        return $obj;
    }

    public function getCollectionsOfObjects($ids){
    	$collections = DB::table('ca_objects_x_collections')
			->whereIn('object_id',$ids)
			->select('collection_id')
			->pluck('collection_id')
			->toArray();
    	return array_unique($collections);
	}

	public function generate_params($type = 'photo', $collection = false, $date = false, $technique = false){
		$params = array();
		$params['type'] = $type;
		$params['relations'] = array();
		if($collection) {
			if(is_array($collection)){
				for($i = 0; $i < count($collection); $i++){
					$params['relations']['collections']['values'][$i] = $collection[$i];
				}
			}else {
				$params['relations']['collections']['values'] = array(
					0 => $collection,
				);
			}
			$params['relations']['collections']['singular'] = 'collection';
			$params['relations']['collections']['plural'] = 'collections';
		}
		$params['attributes'] = array();
		if($date){
			$params['attributes']['dates_from_to'] = array();
			$params['attributes']['dates_from_to']['value'] = $date;
			$params['attributes']['dates_from_to']['type'] = 'integer';
		}
		$params['attributes']['lists'] = array();
		if($technique) {
			$params['attributes']['lists']['technique'] = array();
			if(is_array($technique)){
				for($i = 0; $i < count($technique); $i++){
					$params['attributes']['lists']['technique'][$i] = $technique[$i];
				}
			}else {
				$params['attributes']['lists']['technique'] = array(
					0 => $technique,
				);
			}
		}
		return $params;
	}
}
