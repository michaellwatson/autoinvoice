<?php
use \Illuminate\Database\Eloquent\Model as Eloquent;

class Messagemodel extends Eloquent {

	protected $primaryKey = "ad_id";
    protected $table = "messages"; // table name
/*
    public function user()
    {
        return $this->hasOne('User', 'taken_by');
    }
*/
}