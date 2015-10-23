<?php namespace Foothing\Wrappr\Providers\Permissions;

use BeatSwitch\Lock\Manager;
use Foothing\Wrappr\Providers\Users\UserProviderInterface;

class LockProvider implements PermissionProviderInterface {
	protected $manager;
	protected $userProvider;

	function __construct(Manager $manager, UserProviderInterface $userProvider) {
		$this->manager = $manager;
		$this->userProvider = $userProvider;
	}

	function check($user, $permissions, $resourceName = null, $resourceId = null) {
		return $this->manager->caller($user)->can($permissions, $resourceName, (int)$resourceId);
	}

	function grantRole($roleName, $permission, $resourceName = null) {
		$this->manager->role($roleName)->allow($permission);
	}

}
