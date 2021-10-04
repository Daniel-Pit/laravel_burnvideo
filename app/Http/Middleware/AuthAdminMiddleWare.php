<?php

namespace burnvideo\Http\Middleware;

use Closure;
use Sentinel;
use Redirect;

class AuthAdminMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		if (Sentinel::check()) {
			$user = Sentinel::getUser();
			$admin = Sentinel::findRoleByName('Admin');

			if (!$user->inRole($admin))
			{
				return Redirect::to('admin/login')->withErrors(array('message' => 'You are not authorized to Admin.'));
			}
		} else {
			return Redirect::to('admin/login');
		}

        return $next($request);
    }
}
