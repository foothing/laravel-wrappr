<?php

return [

	'permissionsProvider' => 'Foothing\Wrappr\Providers\Permissions\LockProvider',

	'usersProvider' => 'Foothing\Wrappr\Providers\Users\DefaultProvider',

	// Redirect response when check fails and request is not ajax.
	'redirect' => '/',

	'install' => [
		'routes' => [
//		[
//			'verb' => 'post',
//			'path' => 'api/v1/resources/users',
//			'permissions' => 'admin.account',
//			'resource' => 'user',
//		],
//		[
//			'verb' => 'get',
//			'path' => 'api/v1/resources/users',
//			'permissions' => ['admin.account', 'posts.create'],
//			'resource' => 'user',
//		],
		],

		'permissions' => [
//		['name' => 'api.read', 'roles' => 'users'],
//		['name' => 'admin.account', 'roles' => ['admins', 'editors'], 'resource' => 'user'],
//		['name' => 'other', 'roles' => 'userManager', 'resource' => 'user'],
		]
	],

];