<?php
use \Illuminate\Database\Eloquent\Model as Eloquent;

class Advertfieldsmodel extends Eloquent {
	protected $primaryKey 	= "adv_id";
    protected $table 		= "advert_fields"; // table name

    public function fieldType()
    {
        return $this->hasOne('AdvertFieldsType', 'fi_id', 'adv_field_type');
    }

    public function getInfoByIdEloquent($id){
        $data = Advertfieldsmodel::where('adv_id', $id)->first();
        return $data;
    }

    public function getDataByAdvCategory($advCategoryId){
        $data = Advertfieldsmodel::where('adv_category', $advCategoryId)->get();
        return $data;
    }

    public function getDataByAdvFieldType($advFieldType){
        $data = Advertfieldsmodel::where('adv_field_type', $advFieldType)->get();
        return $data;
    }
}