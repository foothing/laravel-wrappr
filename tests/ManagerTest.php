<?php

use Foothing\Wrappr\Routes\Route;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ManagerTest extends \PHPUnit_Framework_TestCase  {

	protected $manager, $routes, $users, $permissions;

	function setUp() {
		parent::setUp();
		$this->routes = \Mockery::mock('alias:Foothing\Wrappr\Routes\RouteRepository');
		$this->users = \Mockery::mock('Foothing\Wrappr\Providers\Users\UserProviderInterface');
		$this->permissions = \Mockery::mock('Foothing\Wrappr\Providers\Permissions\PermissionProviderInterface');
		$this->manager = new \Foothing\Wrappr\Manager($this->routes, $this->permissions, $this->users);
	}

	function test_superadmin_always_passes() {
		$this->users->shouldReceive('getAuthUser')->once()->andReturn(Mocks::user());
		$this->users->shouldReceive('isSuperAdmin')->once()->andReturn(true);
		$route = new Route(['verb' => 'get', 'pattern' => 'test']);
		$this->assertTrue( $this->manager->check($route) );
	}

	function test_check_fails_for_unauth_users() {
		$this->users->shouldReceive('getAuthUser')->andReturn( null );
		$route = new Route(['verb' => 'get', 'pattern' => 'test']);
		$this->assertFalse( $this->manager->check($route) );
	}

	function test_check_returns_true() {
		$this->users->shouldReceive('getAuthUser')->andReturn( Mocks::user() );
		$this->users->shouldReceive('isSuperAdmin')->once()->andReturn(false);
		$this->permissions->shouldReceive('check')->once()->andReturn(true);
		$route = new Route(['verb' => 'get', 'pattern' => 'test']);
		$this->assertTrue( $this->manager->check($route) );
	}

	function test_check_returns_false() {
		$this->users->shouldReceive('getAuthUser')->andReturn( Mocks::user() );
		$this->users->shouldReceive('isSuperAdmin')->once()->andReturn(false);
		$this->permissions->shouldReceive('check')->once()->andReturn(false);
		$route = new Route(['verb' => 'get', 'pattern' => 'test']);
		$this->assertFalse( $this->manager->check($route) );
	}

	function test_check_passes_if_route_not_found() {
		$this->users->shouldReceive('getAuthUser')->andReturn( null );
		$this->routes->shouldReceive('paginate')->andReturn( null );
		$this->assertTrue( $this->manager->checkPath('get', 'test') );
	}

	function testBestMatch() {
		$this->users->shouldReceive('getAuthUser')->andReturn( null );
		$parser = new \Foothing\Wrappr\Installer\Parser();

		$routes = (object)[
			'data' => [
				$parser->parsePattern("api/v1/users/{id}/*"),
				$parser->parsePattern("api/v1/users/{id}"),
				$parser->parsePattern("api/v1/*"),
				$parser->parsePattern("api/v1"),
				$parser->parsePattern("api/*"),
				$parser->parsePattern("*"),
			]
		];
		$this->routes->shouldReceive('paginate')->andReturn( $routes );
		$this->assertEquals(".*", $this->manager->bestMatch('get', 'test')->pattern );
		$this->assertEquals("api/.*", $this->manager->bestMatch('get', 'api/1')->pattern );
		$this->assertEquals("api/.*", $this->manager->bestMatch('get', 'api/a')->pattern );
		$this->assertEquals("api/.*", $this->manager->bestMatch('get', 'api/v')->pattern );
		$this->assertEquals("api/v1", $this->manager->bestMatch('get', 'api/v1')->pattern );
		$this->assertEquals("api/v1", $this->manager->bestMatch('get', 'api/v1/')->pattern );
		$this->assertEquals("api/v1/.*", $this->manager->bestMatch('get', 'api/v1/whatever/1')->pattern );
		$this->assertEquals("api/v1/.*", $this->manager->bestMatch('get', 'api/v1/users/')->pattern );
		$this->assertEquals("api/v1/users/[0-9]+", $this->manager->bestMatch('get', 'api/v1/users/10')->pattern );
		$this->assertEquals("api/v1/users/[0-9]+/.*", $this->manager->bestMatch('get', 'api/v1/users/10/whatever')->pattern );
	}

	function tearDown() {
		\Mockery::close();
	}
}

class Mocks {

	public static function superadmin() {
		return \Mockery::mock('alias:Foothing\Wrappr\SuperAdminableInterface')
			->shouldReceive('isSuperAdmin')
			->andReturn(true)
			->getMock();
	}

	public static function user() {
		return (object)['id' => 1];
	}

	public static function route($resourceId = null, $resourceName = null) {
		return new \Foothing\Wrappr\Routes\Route(['resourceId' => $resourceId, 'resourceName' => $resourceName]);
	}
}