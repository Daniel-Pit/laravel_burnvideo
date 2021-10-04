<?php
namespace burnvideo\Models;

use Eloquent;

class Transaction extends Eloquent {

    protected $table = 'transaction';
    protected $fillable = ['uid', 'oid', 'ttype', 'price', 'payorg', 'paytime','promocode_id','final_price'];
}