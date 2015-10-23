<?php namespace Foothing\Wrappr\Commands;

use Foothing\Wrappr\Installer\PermissionInstaller;
use Foothing\Wrappr\Installer\RouteInstaller;
use Illuminate\Console\Command;

class Install extends Command {

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'wrappr:install';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Bind routes to permissions.';

	/**
	 * @var \Foothing\Wrappr\Installer\RouteInstaller
	 */
	protected $routes;

	/**
	 * @var \Foothing\Wrappr\Installer\PermissionInstaller
	 */
	protected $permissions;

	public function __construct(RouteInstaller $routes, PermissionInstaller $permissions) {
		parent::__construct();
		$this->routes = $routes;
		$this->permissions = $permissions;
	}

	public function handle() {
		\DB::table('routes')->truncate();
		try {
			foreach(config('wrappr.install.routes') as $route) {
				$this->routes->route($route['verb'], $route['path'])->requires($route['permissions']);
				if (isset($route['resource'])) {
					$this->routes->on($route['resource']);
				}
			}

			$this->routes->install();

			// @TODO remove permission installer, doesn't make sense.

			/*
			foreach(config('wrappr.install.permissions') as $permission) {
				$this->permissions->permission($permission['name'])->to($permission['roles']);
				if (isset($permission['resource'])) {
					$this->permissions->on($permission['resource']);
				}
			}*/


			//$this->permissions->install();

		} catch (\Exception $ex) {
			print ($ex->getTraceAsString());
		}
	}

}