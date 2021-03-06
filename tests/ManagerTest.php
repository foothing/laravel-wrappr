<?php namespace Foothing\Wrappr\Tests;

use Foothing\Wrappr\Installer\Parser;
use Foothing\Wrappr\Routes\Route;
use Foothing\Wrappr\Tests\Mocks\Mocks;
use Foothing\Wrappr\Tests\Mocks\Routes;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ManagerTest extends \PHPUnit_Framework_TestCase  {

    protected $manager;
    protected $routes;
    protected $users;
    protected $permissions;
    protected $cache;

    public function setUp() {
        parent::setUp();
        $this->routes = \Mockery::mock('alias:Foothing\Wrappr\Routes\RouteRepository');
        $this->users = \Mockery::mock('Foothing\Wrappr\Providers\Users\UserProviderInterface');
        $this->permissions = \Mockery::mock('Foothing\Wrappr\Providers\Permissions\PermissionProviderInterface');
        $this->cache = \Mockery::mock('Foothing\Wrappr\Cache\CacheManager');
        $this->manager = new \Foothing\Wrappr\Manager($this->routes, $this->permissions, $this->users, $this->cache);
    }

    public function test_superadmin_always_passes() {
        $this->users->shouldReceive('getAuthUser')->once()->andReturn(Mocks::user());
        $this->users->shouldReceive('isSuperAdmin')->once()->andReturn(true);
        $route = new Route(['verb' => 'get', 'pattern' => 'test']);
        $this->assertTrue($this->manager->check($route));
    }

    public function test_check_fails_for_unauth_users() {
        $this->users->shouldReceive('getAuthUser')->andReturn(null);
        $route = new Route(['verb' => 'get', 'pattern' => 'test']);
        $this->assertFalse($this->manager->check($route));
    }

    public function test_check_returns_true() {
        $this->users->shouldReceive('getAuthUser')->andReturn(Mocks::user());
        $this->users->shouldReceive('isSuperAdmin')->once()->andReturn(false);
        $this->permissions->shouldReceive('check')->once()->andReturn(true);
        $route = new Route(['verb' => 'get', 'pattern' => 'test']);
        $this->assertTrue($this->manager->check($route));
    }

    public function test_check_returns_false() {
        $this->users->shouldReceive('getAuthUser')->andReturn(Mocks::user());
        $this->users->shouldReceive('isSuperAdmin')->once()->andReturn(false);
        $this->permissions->shouldReceive('check')->once()->andReturn(false);
        $route = new Route(['verb' => 'get', 'pattern' => 'test']);
        $this->assertFalse($this->manager->check($route));
    }

    public function test_check_passes_if_route_not_found() {
        $this->users->shouldReceive('getAuthUser')->andReturn(null);
        $this->cache->shouldReceive('get')->andReturn(null);
        $this->routes->shouldReceive('getOrderedRoutes')->andReturn(null);
        $this->assertTrue($this->manager->checkPath('get', 'test'));
    }

    public function testBestMatch() {
        $parser = new Parser();
        $routes = [
            $parser->parsePattern("api/v1/users/{id}/*"),
            $parser->parsePattern("api/v1/users/{id}"),
            $parser->parsePattern("api/v1/*"),
            $parser->parsePattern("api/v1"),
            $parser->parsePattern("api/*"),
            $parser->parsePattern("*"),
        ];
        $this->cache->shouldReceive('get')->andReturn(null);
        $this->cache->shouldReceive('put');
        $this->users->shouldReceive('getAuthUser')->andReturn(null);
        $this->routes->shouldReceive('getOrderedRoutes')->andReturn($routes);
        $this->assertEquals(".*", $this->manager->bestMatch('get', 'test')->pattern);
        $this->assertEquals("api/.*", $this->manager->bestMatch('get', 'api/1')->pattern);
        $this->assertEquals("api/.*", $this->manager->bestMatch('get', 'api/a')->pattern);
        $this->assertEquals("api/.*", $this->manager->bestMatch('get', 'api/v')->pattern);
        $this->assertEquals("api/v1", $this->manager->bestMatch('get', 'api/v1')->pattern);
        $this->assertEquals("api/v1", $this->manager->bestMatch('get', 'api/v1/')->pattern);
        $this->assertEquals("api/v1/.*", $this->manager->bestMatch('get', 'api/v1/whatever/1')->pattern);
        $this->assertEquals("api/v1/.*", $this->manager->bestMatch('get', 'api/v1/users/')->pattern);
        $this->assertEquals("api/v1/users/[0-9]+", $this->manager->bestMatch('get', 'api/v1/users/10')->pattern);
        $this->assertEquals("api/v1/users/[0-9]+/.*", $this->manager->bestMatch('get', 'api/v1/users/10/whatever')->pattern);
    }

    public function test_bestMatch_read_from_cache() {
        $route = new Route(['pattern' => 'foo']);
        $route->id = 101;

        $this->cache->shouldReceive('get')->once()->andReturn($route);
        $match = $this->manager->bestMatch('get', 'test');

        $this->assertEquals($route->id, $match->id);
        $this->assertEquals($route->pattern, $match->pattern);
    }

    public function test_bestMatch_cache_routes() {
        $parser = new Parser();
        $route = $parser->parsePattern('foo/bar');

        $this->cache->shouldReceive('get')->andReturn(null);
        $this->routes->shouldReceive('getOrderedRoutes')->andReturn([$route]);
        $this->cache->shouldReceive('put');

        $this->manager->bestMatch('get', 'foo/bar');
    }
}
