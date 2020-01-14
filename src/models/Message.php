<?php

namespace mywishlist\models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model {
    protected $table = "message";
    protected $primaryKey = "id";
    public $timestamps = false;

    public function user() {
        return $this->belongsTo('\mywishlist\models\Account', 'account_id');
    }

    public function list() {
        return $this->hasMany('\mywishlist\models\Liste', 'liste_id')->get();
    }
}