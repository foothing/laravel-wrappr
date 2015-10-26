<?php namespace Foothing\Wrappr;

use Illuminate\Support\ServiceProvider;

class WrapprServiceProvider extends ServiceProvider {

	public function register() {
		$this->app->bind('foothing.wrappr', 'Foothing\Wrappr\Manager');
		$this->app->bind('foothing.wrappr.installer', 'Foothing\Wrappr\Installer\RouteInstaller');

		// Interfaces binding.
		$this->app->bind('Foothing\Wrappr\Providers\Permissions\PermissionProviderInterface', config('wrappr.permissionsProvider'));
		$this->app->bind('Foothing\Wrappr\Providers\Users\UserProviderInterface', config('wrappr.usersProvider'));
	}

	public function boot() {
		$this->publishes( [ __DIR__ . "/../../database/" => database_path('/migrations') ], 'migrations' );
		$this->publishes( [ __DIR__ . "/../../config/routes.php" => config_path('routes.php') ], 'config' );
	}
}