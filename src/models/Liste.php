<?php


namespace mywishlist\models;


use Illuminate\Database\Eloquent\Model;

class Liste extends Model{
    protected $table = "liste";
    protected $primaryKey = "num";
    public $timestamps = false;

    public function items() {
        return $this->hasMany('\mywishlist\models\Item', 'liste_id');
    }

    public static function generateToken($length = 8) {
        $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
        $codeAlphabet .= '0123456789';
        $max = strlen($codeAlphabet);

        do {
            $token = '';
            for ($i = 0; $i < $length; $i++) {
                $token .= $codeAlphabet[random_int(0, $max - 1)];
            }
        } while(!is_null(Liste::select('num')->where('token', '=', $token)->first()));

        return $token;
    }
}