<?php namespace Foothing\Wrappr\Tests\Permissions;

use Foothing\Wrappr\Permissions\Collection;

class CollectionTest extends \PHPUnit_Framework_TestCase {

    public function testAllow() {
        $collection = new Collection();
        $collection->allow('eat', 'beef', 1);
        $this->assertEquals(1, $collection->countAllowed());
        $this->assertEquals('eat', $collection->getAllowed(0)->name);
        $this->assertEquals('beef', $collection->getAllowed(0)->resourceName);
        $this->assertEquals(1, $collection->getAllowed(0)->resourceId);
    }

    public function testDeny() {
        $collection = new Collection();
        $collection->deny('eat', 'beef', 1);
        $this->assertEquals(1, $collection->countDenied());
        $this->assertEquals('eat', $collection->getDenied(0)->name);
        $this->assertEquals('beef', $collection->getDenied(0)->resourceName);
        $this->assertEquals(1, $collection->getDenied(0)->resourceId);
    }

    public function testToJson() {
        $collection = new Collection();
        $collection->allow('eat', 'beef', 1);
        $collection->deny('drink', 'beer', 1);
        $json = $collection->toJson();
        $object = json_decode($json);
        $this->assertNotEmpty($object->allowed);
        $this->assertNotEmpty($object->denied);
        $this->assertEquals('eat', $object->allowed[0]->name);
        $this->assertEquals('beef', $object->allowed[0]->resourceName);
        $this->assertEquals(1, $object->allowed[0]->resourceId);
        $this->assertEquals('drink', $object->denied[0]->name);
        $this->assertEquals('beer', $object->denied[0]->resourceName);
        $this->assertEquals(1, $object->denied[0]->resourceId);
    }
}
