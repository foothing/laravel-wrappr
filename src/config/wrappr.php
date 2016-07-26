<?php

return [

    // Actually the only available provider is with Lock.
	'permissionsProvider' => 'Foothing\Wrappr\Providers\Permissions\LockProvider',

    // Available providers:
    // - Foothing\Wrappr\Providers\Users\DefaultProvider
    // - Foothing\Wrappr\Providers\Users\SentinelUserProvider
	'usersProvider' => 'Foothing\Wrappr\Providers\Users\DefaultProvider',

	// Redirect response when check fails and request is not ajax.
    // Note: the request type is detected in the Laravel way, that
    // checks for the X-Requested-With HTTP Header. If you are
    // using client libraries such as angularjs keep in mind that
    // you'll need to add that header your self.
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

	],

];