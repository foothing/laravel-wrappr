<?php namespace Foothing\Wrappr\Tests\Permissions;

use Foothing\Wrappr\Permissions\Permission;

class PermissionTest extends \PHPUnit_Framework_TestCase {

    function testEquals() {
        $permission0 = new Permission('foo', 'bar', 'baz');
        $permission1 = new Permission('foo');
        $permission2 = new Permission('foo', 'bar');
        $permission3 = new Permission('foo', 'bar', 'baz');
        $this->assertFalse($permission0->equals($permission1));
        $this->assertFalse($permission0->equals($permission2));
        $this->assertTrue($permission0->equals($permission3));
    }

}