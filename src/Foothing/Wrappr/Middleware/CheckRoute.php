<?php namespace Foothing\Wrappr\Middleware;

use Foothing\Wrappr\Routes\Route;

class CheckRoute extends AbstractMiddleware {

	function handle($request, \Closure $next, $permissions, $resource = null, $id = null) {

		$route = new Route();
		$route->permissions = $permissions;
		$route->resourceName = $resource;

		if ($id && preg_match("/^\{.+\}$/", $id)) {
			$id = preg_replace("/\{|\}/", "", $id);
			$route->resourceId = $request->route()->getParameter($id);
		} else if ($id) {
			$route->resourceId = $id;
		}

		if ( ! $this->manager->check($route) ) {
			return $this->error();
		}

		return $next($request);

	}

}