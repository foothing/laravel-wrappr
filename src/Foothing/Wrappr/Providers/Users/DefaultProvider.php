<?php namespace Foothing\Wrappr\Providers\Users;

use Illuminate\Auth\Guard;

class DefaultProvider implements UserProviderInterface {
	protected $guard;

	function __construct(Guard $guard) {
		$this->guard = $guard;
	}

	function getAuthUser() {
		return $this->guard->user();
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
			$roles[] = $role->label;
		}
		return $roles;
	}
}