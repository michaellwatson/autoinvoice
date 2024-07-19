<?php
use \Illuminate\Database\Eloquent\Model as Eloquent;

class Permissions extends Eloquent {

    protected $table = "users_permissions"; // table name
    /*
    public function permissions()
    {
        return $this->hasOne('UserPermission');
    }
    */
}