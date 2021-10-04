<?php
namespace burnvideo\Models;

use Eloquent;

class OrderShipping extends Eloquent {

    protected $table = 'order_shipping';
    protected $fillable = [ 'orderid', 'firstname', 'lastname', 'street', 'city', 'state', 'zipcode', 'dvdcount', 'created_at', 'updated_at'];
}