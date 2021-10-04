<?php
namespace burnvideo\Models;

use Eloquent;

class OrderFile extends Eloquent {

    protected $table = 'order_file';
    protected $fillable = ['oid', 'fid'];
}