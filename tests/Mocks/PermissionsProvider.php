<?php namespace Foothing\Wrappr\Tests\Mocks;

use Foothing\Wrappr\Providers\Permissions\AbstractProvider;

class PermissionsProvider extends AbstractProvider
{
    public function check($user, $permissions, $resourceName = null, $resourceId = null) {}

    public function can($permissions, $resourceName = null, $resourceId = null) {}

    public function user($user) { }

    public function role($role) { }

    public function all() { }

    public function grant($permissions, $resourceName = null, $resourceId = null) { }

    public function revoke($permissions, $resourceName = null, $resourceId = null) {}
}
