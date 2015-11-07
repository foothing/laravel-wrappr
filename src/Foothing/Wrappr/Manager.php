<?php namespace Foothing\Wrappr;

use Foothing\Wrappr\Installer\Parser;
use Foothing\Wrappr\Providers\Permissions\PermissionProviderInterface;
use Foothing\Wrappr\Providers\Users\UserProviderInterface;
use Foothing\Wrappr\Routes\Route;
use Foothing\Wrappr\Routes\RouteRepository;

class Manager {

	protected $routes;
	protected $permissionProvider;
	protected $userProvider;
	protected $parser;

	function __construct(RouteRepository $routes, PermissionProviderInterface $permissionProvider, UserProviderInterface $userProvider) {
		$this->routes = $routes;
		$this->permissionProvider = $permissionProvider;
		$this->userProvider = $userProvider;
		$this->parser = new Parser();
	}

	function getUser() {
		return $this->userProvider->getAuthUser();
	}

	function checkPath($verb, $path, $user = null) {
		// Find the route.
		$route = $this->bestMatch($verb, $path);

		// When a route is not found we assume it has no permissions bound.
		if ( ! $route ) {
			return true;
		}

		// Extract the resource id from the given path.
		$route->resourceId = $this->parser->getResourceFromPath($route, $path);

		// Check the route.
		return $this->check($route, $user);
	}

	function check(Route $route, $user = null) {
		$user = $user ?: $this->getUser();

		// Returning false when auth user is empty.
		if ( ! $user ) {
			return false;
		}

		// Superadmin must always pass.
		if ($user && $this->userProvider->isSuperAdmin($user)) {
			return true;
		}

		return $this->permissionProvider->check($user, $route->permissions, $route->resourceName, $route->resourceId);
	}

	function bestMatch($verb, $path) {
		if (! $routes = $this->routes->getOrderedRoutes($verb)) {
			return null;
		}

		$replacedPath = $this->replace( $this->parser->trimPath($path) );
		foreach ($routes as $route) {
			$replacedPattern = $this->replace($route->pattern);
			if (preg_match("/^$replacedPattern$/", $replacedPath)) {
				return $route;
			}
		}

		return null;
	}

	protected function replace ($path) {
		$path = preg_replace("/\//", "___", $path);
		return $path;
	}
}