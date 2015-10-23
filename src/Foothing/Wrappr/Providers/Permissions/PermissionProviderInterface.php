<?php namespace Foothing\Wrappr\Providers\Permissions;


interface PermissionProviderInterface {

	function check($user, $permissions, $resourceName = null, $resourceId = null);

	function grantRole($roleName, $permissions, $resourceName = null);
}