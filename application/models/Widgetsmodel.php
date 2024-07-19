<?php
use \Illuminate\Database\Eloquent\Model as Eloquent;

class Widgetsmodel extends Eloquent {
	protected $primaryKey 	= "id";
    protected $table 		= "widgets"; // table name

       public function conditions()
    {
        return $this->hasMany('Widgetsconditionsmodel', 'widget_id', 'id');
    }
}