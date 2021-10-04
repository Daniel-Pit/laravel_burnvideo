<?php

namespace burnvideo\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        'check-promo',
        'ajax-pay',
        'api/*',
        'admin/api_addUser',
        'admin/api_addPromoCode',
        'admin/api_editUser',
        'admin/api_editPromoCode',
        'admin/api_getUser',
        'admin/api_getPromoCode',
        'admin/api_deleteUser',
        'admin/api_getMessage',
        'admin/api_deleteMessage',
        'admin/api_getNotify',
        'admin/api_deleteNotify',
        'admin/api_updateSet',
        'admin/api_deleteOrder',
        'admin/api_sendOrder',
        'admin/api_convertOrder',
        'admin/api_cancelOrder',
        'admin/api_getShipArray',
        'admin/api_searchUsers',
        'admin/api_sendMail',
        'admin/api_sendMailById',
        'admin/api_sendNotify',
        'admin/api_sendNotifyById',
        'admin/api_stateList',
        'admin/api_cityList',
        'admin/api_uploadMailImage',  
        'admin/api_setOrderDvdTitle',
        'admin/api_addCalendarEvent',
        'admin/api_deleteCalendarEvent',
        'admin/api_editAdmin',
        'admin/api_deletes3',


        
    ];
	
	/*
	public function handle($request, Closure $next)
	{
		if ( ! $request->is('api/*'))
		{
			return parent::handle($request, $next);
		}

		return $next($request);
	}*/
}
