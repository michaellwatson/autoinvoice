<?php
use \Illuminate\Database\Eloquent\Model as Eloquent;

class UserEl extends Eloquent {
	protected $primaryKey = "us_id";
    protected $table = "user"; // table name
	
	public function userpermissions()
    {
        return $this->hasMany('UserPermissions', 'user_id');
    }

}