<?php namespace Foothing\Wrappr\Middleware;

use Foothing\Wrappr\Manager;

class AbstractMiddleware {
	protected $manager;

	function __construct(Manager $manager) {
		$this->manager = $manager;
	}

	function error($request) {
		if ($request->ajax()) {
			return response(null, 401)->header('X-Reason', 'permissions');
		} else if ( $url = config('wrappr.redirect') ) {
			return response()->redirectTo($url);
		} else {
			return response("Unauthorized.", 401);
		}
	}

}