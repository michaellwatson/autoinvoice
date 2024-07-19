<?php
use \Illuminate\Database\Eloquent\Model as Eloquent;

class Stayonpagemodel extends Eloquent {
	protected $primaryKey 	= "id";
    protected $table 		= "stay_on_page"; // table name

       public function conditions()
    {
        return $this->hasMany('UserEl', 'us_id', 'id');
    }
}