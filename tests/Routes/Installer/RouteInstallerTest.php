<?php namespace Foothing\Tests\Routes\Installer;

use Foothing\Wrappr\Tests\BaseTestCase;

class RouteInstallerTest extends BaseTestCase {

    public function testRouteIsSaved() {
        $this->routeInstaller
            ->route('get', '/api/v1/*')->requires('api.read')
            ->route('put', '/api/v1/*')->requires('api.write')
            ->route('delete', '/api/v1/users/{id}/*')->requires('api.write,api.read')->on('users')
            ->install();

        $repository = new \Foothing\Wrappr\Routes\RouteRepository(new \Foothing\Wrappr\Routes\Route(), new \Foothing\Wrappr\Installer\Parser());
        $routes = $repository->all();
        $this->assertEquals(3, count($routes));
        $this->assertEquals('get', $routes[0]->verb);
        $this->assertEquals('api.read', $routes[0]->permissions[0]);
        $this->assertEquals('put', $routes[1]->verb);
        $this->assertEquals('api.write', $routes[1]->permissions[0]);
        $this->assertEquals('delete', $routes[2]->verb);
        $this->assertEquals('api.write', $routes[2]->permissions[0]);
        $this->assertEquals('api.read', $routes[2]->permissions[1]);
        $this->assertEquals('users', $routes[2]->resourceName);
    }

    public function testRouteWildcard() {
        $this->routeInstaller->route('*', '/api/v1/*')->requires('api.read')->install();
        $repository = new \Foothing\Wrappr\Routes\RouteRepository(new \Foothing\Wrappr\Routes\Route(), new \Foothing\Wrappr\Installer\Parser());
        $routes = $repository->all();
        $this->assertEquals(4, count($routes));
        $this->assertEquals('get', $routes[0]->verb);
        $this->assertEquals('api.read', $routes[0]->permissions[0]);
        $this->assertEquals('post', $routes[1]->verb);
        $this->assertEquals('api.read', $routes[1]->permissions[0]);
        $this->assertEquals('put', $routes[2]->verb);
        $this->assertEquals('api.read', $routes[2]->permissions[0]);
        $this->assertEquals('delete', $routes[3]->verb);
        $this->assertEquals('api.read', $routes[3]->permissions[0]);
    }

    public function testWeirdRouteInput() {
        try {
            $this->routeInstaller->route(null, null);
            $this->fail('Exception not raised.');
        } catch (\Exception $ex) {

        }

        try {
            $this->routeInstaller->route('invalid', null);
            $this->fail('Exception not raised.');
        } catch (\Exception $ex) {

        }

        try {
            $this->routeInstaller->route(null, 'pattern');
            $this->fail('Exception not raised.');
        } catch (\Exception $ex) {

        }
    }
}