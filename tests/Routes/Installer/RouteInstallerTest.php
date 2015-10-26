<?php
class RouteInstallerTest extends BaseTestCase {

	function setUp() {
		parent::setUp();
	}

	function test_route_is_saved() {
		$this->routeInstaller
			->route('get', '/api/v1/*')->requires('api.read')
			->route('put', '/api/v1/*')->requires('api.write')
			->route('delete', '/api/v1/users/{id}/*')->requires('api.write,api.read')->on('users')
			->install();

		$repository = new \Foothing\Wrappr\Routes\RouteRepository( new \Foothing\Wrappr\Routes\Route(), new \Foothing\Wrappr\Installer\Parser() );
		$routes = $repository->all();
		$this->assertEquals( 3, count($routes) );
		$this->assertEquals( 'get', $routes[0]->verb );
		$this->assertEquals( 'api.read', $routes[0]->permissions[0] );
		$this->assertEquals( 'put', $routes[1]->verb );
		$this->assertEquals( 'api.write', $routes[1]->permissions[0] );
		$this->assertEquals( 'delete', $routes[2]->verb );
		$this->assertEquals( 'api.write', $routes[2]->permissions[0] );
		$this->assertEquals( 'api.read', $routes[2]->permissions[1] );
		$this->assertEquals( 'users', $routes[2]->resourceName);
	}

	function test_route_wildcard() {
		$this->routeInstaller->route('*', '/api/v1/*')->requires('api.read')->install();
		$repository = new \Foothing\Wrappr\Routes\RouteRepository( new \Foothing\Wrappr\Routes\Route(), new \Foothing\Wrappr\Installer\Parser() );
		$routes = $repository->all();
		$this->assertEquals( 4, count($routes) );
		$this->assertEquals( 'get', $routes[0]->verb );
		$this->assertEquals( 'post', $routes[1]->verb );
		$this->assertEquals( 'put', $routes[2]->verb );
		$this->assertEquals( 'delete', $routes[3]->verb );
	}

	function _testMake() {
		$config = [
			'routes' => [
				[
					'verb' => 'post',
					'path' => 'api/v1/resources/users',
					'permissions' => 'admin.account',
					'resource' => 'user',
				],
				[
					'verb' => 'get',
					'path' => 'api/v1/resources/users',
					'permissions' => ['admin.account', 'posts.create'],
					'resource' => 'user',
				],
			],
			'permissions' => [
				['permission' => 'api.read', 'roles' => 'users'],
				['permission' => 'admin.account', 'roles' => ['admins', 'editors'], 'resource' => 'user'],
				['permission' => 'other', 'roles' => 'userManager', 'resource' => 'user'],
			]
		];

		$installer = $this->routeInstaller->make($config);
		$routes = $installer->getRoutes();
		$permissions = $installer->getPermissions();

		$this->assertEquals(2, count($routes));
		$this->assertEquals('api/v1/resources/users', $routes[0]->pattern);
		$this->assertEquals('post', $routes[0]->verb);
		$this->assertEquals('user', $routes[0]->resourceName);
		$this->assertEquals(1, count($routes[0]->permissions));
		$this->assertEquals('admin.account', $routes[0]->permissions[0]);
		$this->assertEquals(2, count($routes[1]->permissions));
		$this->assertEquals('admin.account', $routes[1]->permissions[0]);
		$this->assertEquals('posts.create', $routes[1]->permissions[1]);
		$this->assertEquals(3, count($permissions));
		$this->assertEquals('users', $permissions['api.read']['roles'][0]);
		$this->assertEquals('admins', $permissions['admin.account']['roles'][0]);
		$this->assertEquals('editors', $permissions['admin.account']['roles'][1]);
	}

	function test_weird_route_input() {
		try {
			$this->routeInstaller->route(null, null);
			$this->fail('Exception not raised.');
		} catch (\Exception $ex) {}

		try {
			$this->routeInstaller->route('invalid', null);
			$this->fail('Exception not raised.');
		} catch (\Exception $ex) {}

		try {
			$this->routeInstaller->route(null, 'pattern');
			$this->fail('Exception not raised.');
		} catch (\Exception $ex) {}
	}

	public function tearDown() {
		Mockery::close();
	}

}