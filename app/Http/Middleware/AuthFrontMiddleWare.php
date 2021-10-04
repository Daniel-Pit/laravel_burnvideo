<?php

namespace burnvideo\Http\Middleware;

use Closure;
use Sentinel;
use Redirect;

class AuthFrontMiddleWare
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
			if ($user->inRole($admin))
			{
				return Redirect::to('login')->withErrors(array('message' => 'Admin is not authorized to User.'));
			}
		} else {
			return Redirect::to('login');
		}
        return $next($request);
    }
}
