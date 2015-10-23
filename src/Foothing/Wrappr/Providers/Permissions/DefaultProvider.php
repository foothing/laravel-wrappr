<?php namespace Foothing\Wrappr\Providers\Permissions;


class DefaultProvider implements PermissionProviderInterface {

	function check($user, $permissions, $resourceName = null, $resourceId = null) {
		// TODO: Implement check() method.
	}

	function grantRole($roleName, $permissions, $resourceName = null) {
		// TODO: Implement grantRole() method.
	}
}