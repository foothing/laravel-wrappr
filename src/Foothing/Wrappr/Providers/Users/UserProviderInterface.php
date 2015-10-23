<?php namespace Foothing\Wrappr\Providers\Users;


interface UserProviderInterface {

	function getAuthUser();
	function isSuperAdmin($user);
	function getType($user);
	function getId($user);
	function getRoles($user);
}