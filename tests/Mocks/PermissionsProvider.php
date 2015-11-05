<?php namespace Foothing\Wrappr\Tests\Mocks;

use Foothing\Wrappr\Providers\Permissions\AbstractProvider;

class PermissionsProvider extends AbstractProvider {
    function check($user, $permissions, $resourceName = null, $resourceId = null) {}

    function user($user) { }

    function role($role) { }

    function all() { }

    function grant($permissions, $resourceName = null, $resourceId = null) { }

    function revoke($permissions, $resourceName = null, $resourceId = null) {}
}
