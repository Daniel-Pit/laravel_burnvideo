<?php
namespace burnvideo\Models;

use Eloquent;

class Order extends Eloquent {

    protected $table = 'orders';
    
    protected $primaryKey = 'id';
    protected $fillable = [ 
        'ordertag', 
        'devicetype', 
        'uid', 
        'filecount', 
        'weight', 
        'dvdtitle', 
        'dvdcatption', 
        'status', 
        'created_at', 
        'updated_at', 
        'burn_lock', 
        'burn_app', 
        'burn_app_num', 
        'preorder',
        'downloaded',
        'download_at'
    ];
    
}