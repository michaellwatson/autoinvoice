<?php
use \Illuminate\Database\Eloquent\Model as Eloquent;

class FormsTablesmodel extends Eloquent {
	protected $primaryKey 	= "ft_id";
    protected $table 		= "forms_tables"; // table name

}