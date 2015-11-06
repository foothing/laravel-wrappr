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
}