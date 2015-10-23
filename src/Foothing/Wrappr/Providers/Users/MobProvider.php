<?php namespace Foothing\Wrappr\Providers\Users;

use Foothing\Mob\Mob;

class MobProvider implements UserProviderInterface {
	protected $mob;

	function __construct(Mob $mob) {
		$this->mob = $mob;
	}

	function getAuthUser() {
		return $this->mob->user();
	}

	function isSuperAdmin($user) {
		return $user->id == 1;
	}

	function getType($user) {
		return 'user';
	}

	function getId($user) {
		return $user->id;
	}

	function getRoles($user) {
		$roles = [];
		foreach($user->roles as $role) {
			$roles[] = $role->name;
		}
		return $roles;
	}


}