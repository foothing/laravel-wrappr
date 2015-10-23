<?php namespace Foothing\Wrappr\Middleware;

use Foothing\Wrappr\Manager;

class AbstractMiddleware {
	protected $manager;

	function __construct(Manager $manager) {
		$this->manager = $manager;
	}

	function error() {
		return response(null, 401)->header('X-Reason', 'permissions');
	}

}