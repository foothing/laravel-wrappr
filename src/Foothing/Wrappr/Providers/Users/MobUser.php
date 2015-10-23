<?php namespace Foothing\Wrappr\Providers\Users;


use BeatSwitch\Lock\Callers\Caller;

class MobUser extends \Foothing\Mob\Users\User implements Caller{

	/**
	 * The type of caller
	 *
	 * @return string
	 */
	public function getCallerType() {
		return "user";
	}

	/**
	 * The unique ID to identify the caller with
	 *
	 * @return int
	 */
	public function getCallerId() {
		return $this->id;
	}

	/**
	 * The caller's roles
	 *
	 * @return array
	 */
	public function getCallerRoles() {
		$roles = [];
		foreach($this->roles as $role) {
			$roles[] = $role->label;
		}
		return $roles;
	}
}