<?php
use \Illuminate\Database\Eloquent\Model as Eloquent;

class Eventsmodel extends Eloquent {

    protected $table = "events"; // table name

    public function user()
    {
        return $this->hasOne('UserEl', 'us_id', 'user_id');
    }
}