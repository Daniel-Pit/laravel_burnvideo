<?php
namespace burnvideo\Models;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
//use Cartalyst\Sentry\Users\Eloquent\User as SentinelUserModel;
use Cartalyst\Sentinel\Users\EloquentUser as SentinelUserModel;

class User extends SentinelUserModel {

	protected $fillable = [ 
		'email', 
		'password', 
		'street', 
		'city', 
		'state', 
		'zipcode', 
		'apncode', 
		'gcmcode', 
		'mon_weight', 
		'mon_nextday', 
		'mon_freedvd', 
		'first_ordertime', 
		'token', 
		'customer_id', 
		'permissions', 
		'last_login', 
		'first_name', 
		'last_name', 
		'created_at', 
		'updated_at', 
		'first_ordermail',
		'recv_promomails'
	];

    protected $hidden = [
        'password',
    ];
}

