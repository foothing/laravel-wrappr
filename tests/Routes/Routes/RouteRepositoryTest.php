<?php

class RouteRepositoryTest extends BaseTestCase {

	function test_find_by_path() {
		$repository = new \Foothing\Wrappr\Routes\RouteRepository( new \Foothing\Wrappr\Routes\Route() );
		$this->routeInstaller
			->route('get', 'test')->requires('p0')
			->route('get', 'test/{resource}')->requires('p1')
			->route('get', 'test/{resource}/bar')->requires('p2')
			->route('get', 'test/{resource}/*')->requires('p3')
			->route('get', 'foo/*')->requires('p4')
			->install();
		$this->assertEquals('p0', $repository->findByPath('get', 'test')->permissions[0]);
		$this->assertEquals('p1', $repository->findByPath('get', 'test/1')->permissions[0]);
		$this->assertEquals('p2', $repository->findByPath('get', 'test/1/bar')->permissions[0]);
		$this->assertEquals('p3', $repository->findByPath('get', 'test/1/whatever')->permissions[0]);
		$this->assertEquals('p4', $repository->findByPath('get', 'foo/whatever')->permissions[0]);
		$this->assertNull($repository->findByPath('get', 'foo'));

	}

	public function test_path_is_properly_trimmed() {
		$repository = new \Foothing\Wrappr\Routes\RouteRepository( new \Foothing\Wrappr\Routes\Route() );
		$this->assertEquals($repository->trimPath(""), "");
		$this->assertEquals($repository->trimPath("test"), "test");
		$this->assertEquals($repository->trimPath("/test"), "test");
		$this->assertEquals($repository->trimPath("//test"), "test");
		$this->assertEquals($repository->trimPath("test/"), "test");
		$this->assertEquals($repository->trimPath("test/foo/"), "test/foo");
		$this->setExpectedException('Exception');
		$this->assertEquals($repository->trimPath("test//"), "test");
	}

}