<?php
namespace burnvideo\Http\Controllers;
class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

    /**
	 * Flash msg's. If only we could use laracasts flash..but anyway
     * @param $type
     * @param $message
     */
	protected function flash($type, $message)
	{
		Session::put('flash_notification.message', $message);
		Session::put('flash_notification.level', $type);
	}
}
