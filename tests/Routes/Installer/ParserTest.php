<?php

class ParserTest extends PHPUnit_Framework_TestCase {

	public function test_route_is_properly_parsed() {
		$parser = new \Foothing\Wrappr\Installer\Parser();

		$this->assertEquals($parser->parsePattern("")->pattern, "");
        //$this->assertEquals($parser->parsePattern("*")->pattern, '\*');
		$this->assertEquals($parser->parsePattern("*/ignored1/ignored2")->pattern, '\*');
		$this->assertEquals($parser->parsePattern("test")->pattern, "test");
		$this->assertEquals($parser->parsePattern("test/foo/bar")->pattern, "test/foo/bar");
		$this->assertEquals($parser->parsePattern("test/{resource}")->pattern, "test/[0-9]+");
		$this->assertEquals($parser->parsePattern("test/{resource}/bar")->pattern, "test/[0-9]+/bar");
		$this->assertEquals($parser->parsePattern("test/{resource}/*")->pattern, 'test/[0-9]+/\*');
		$this->assertEquals($parser->parsePattern("test/{resource}/*/ignored")->pattern, 'test/[0-9]+/\*');

		$route = $parser->parsePattern("test/{resource}");
		$this->assertEquals($route->pattern, "test/[0-9]+");
		$this->assertEquals($route->resourceOffset, 1);

		$route = $parser->parsePattern("test/{resource}/bar", true);
		$this->assertEquals($route->pattern, "test/[0-9]+/bar");
		$this->assertEquals($route->resourceOffset, 1);

		$route = $parser->parsePattern("test/{resource}/*", true);
		$this->assertEquals($route->pattern, 'test/[0-9]+/\*');
		$this->assertEquals($route->resourceOffset, 1);

		$route = $parser->parsePattern("test/{resource}/*/ignored", true);
		$this->assertEquals($route->pattern, 'test/[0-9]+/\*');
		$this->assertEquals($route->resourceOffset, 1);

		$this->setExpectedException('Exception');
		$parser->parsePattern("{resource}/*/ignored", true);
	}

	public function testResourcePattern() {
		$parser = new \Foothing\Wrappr\Installer\Parser();
		$route = $parser->parsePattern("admin/{resource}/foo");
		$this->assertEquals($parser->getResourceFromPath($route, "admin/a/foo"), 'a');
		$this->assertEquals($parser->getResourceFromPath($route, "admin/0/foo"), 0);
		$this->assertEquals($parser->getResourceFromPath($route, "admin/1/foo"), 1);
		$this->assertEquals($parser->getResourceFromPath($route, "admin/11/foo"), 11);
		$this->assertEquals($parser->getResourceFromPath($route, "admin/1a/foo"), '1a');
		$this->assertEquals($parser->getResourceFromPath($route, "admin/a1/foo"), 'a1');
	}

}