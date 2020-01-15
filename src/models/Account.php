<?php

namespace mywishlist\models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model {
    protected $table = 'account';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function lists() {
        return $this->hasMany('\mywishlist\models\Liste', 'user_id')->get();
    }

    public function toArray() {
        return $this->getAttributes();
    }

    public function generateResetToken(Account $account) {
        $token = bin2hex(random_bytes(16));
        $account->token_hash = password_hash($token, PASSWORD_DEFAULT);
        $account->token_expire = date("Y-m-d H:i:s", strtotime('+1 hour'));
        $account->save();
        return $token;
    }
}