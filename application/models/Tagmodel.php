<?php
use \Illuminate\Database\Eloquent\Model as Eloquent;

class Tagmodel extends Eloquent {
	protected $primaryKey = "ad_id";
    protected $table = "tags"; // table name
	
    public function schemes()
    {
        return $this->hasMany('Schememodel', 'ad_Tag', 'ad_id');
    }
}