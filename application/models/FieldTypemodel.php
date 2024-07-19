<?php
use \Illuminate\Database\Eloquent\Model as Eloquent;

class FieldTypemodel extends Eloquent {
	protected $primaryKey 	= "fi_id";
    protected $table 		= "field_type"; // table name

}