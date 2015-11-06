<?php namespace Foothing\Wrappr\Tests;

class BaseTestCase extends \Orchestra\Testbench\TestCase {
    protected $routeInstaller;

    protected function getEnvironmentSetUp($app) {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   	=> 'mysql',
            'host' 		=> 'localhost',
            'database' 	=> 'routes',
            'username'	=> 'routes',
            'password'	=> 'routes',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ]);

        $this->routeInstaller = $app->make('foothing.wrappr.installer');
    }

    protected function getPackageProviders($app) {
        $app['config']->set('wrappr.permissionsProvider', 'Foothing\Wrappr\Tests\Mocks\PermissionsProvider');
        $app['config']->set('wrappr.usersProvider', 'Foothing\Wrappr\Providers\Users\DefaultProvider');
        return ['Foothing\Wrappr\WrapprServiceProvider'];
    }

    public function setUp() {
        parent::setUp();
        $this->artisan('migrate', [
            '--database'	=>	'testbench',
            '--realpath'	=> 	realpath(__DIR__ . '/../src/database')
        ]);
    }

    public function tearDown() {
        \Mockery::close();
        \DB::table('routes')->truncate();
    }

    public function testNoWarning() {}
}
