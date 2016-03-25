# Laravel Wrappr

[![Build Status](https://travis-ci.org/foothing/laravel-wrappr.svg?branch=master)](https://travis-ci.org/foothing/laravel-wrappr)
[![Code Climate](https://img.shields.io/codeclimate/github/foothing/laravel-wrappr.svg)](https://travis-ci.org/foothing/laravel-wrappr)
[![Test Coverage](https://codeclimate.com/github/foothing/laravel-wrappr/badges/coverage.svg)](https://codeclimate.com/github/foothing/laravel-wrappr/coverage)

This is a Laravel 5 package that aims to simplify the process of
binding routes to permissions
and it is indepentent from a specific permissions handler, allowing to
add route checks even if your permissions handler
doesn't support this feature natively.

Also, it tries to put some effort in decoupling your app from
the permission handler for what concerns basic operations.

There is a [Lock integration](https://github.com/foothing/laravel-lock-routes)
ready to use.

I've plans to implement a `Gate` integration as well, feel free
to drop a line if you're interested in this.

## Usage example <a href name="usage"></a>

A basic use case where you want to restrict route access to the
`read.users` permission

```php
Route::get('api/users/{id?}', ['middleware:wrappr.check:read.users,user,{id}', function() {
	// Access is allowed to users with the 'read.users' permission on
	// the 'user' resource with the {id} identifier
}]);
```

Or, you can define custom route patterns that can help with dynamic
urls, assuming you have defined a controller and want to split the
access logic on its methods.

```php
Route::controller('api/v1/{args?}', 'FooController');
```

Assuming your controller provides the following routes
```php
GET /api/v1/resources/users
GET /api/v1/resources/posts
POST /api/v1/services/publish/post
```

you can define a rule like the following to restrict access:
```php
[
	'verb' => 'put',
	'path' => 'api/v1/resources/posts/{id}',
	'permissions' => ['posts.create', 'posts.update'],
	'resource' => 'post',
],
```

## Contents
- [Concept](#concept)
- [Install and setup](#setup)
- [Configure the providers](#configure)
- [Use within Laravel Router](#route_basic)
- [Use within custom routes](#route_custom)
- [Install routes with config file](#route_install_config)
- [Install routes programmatically](#route_install_prog)
- [Setup the middleware](#middleware)
	- [A note on route processing order](#route_processing)
- [Middleware Response](#middleware_response)
- [How to develop providers](#providers_develop)
- [License](#license)

## Concept <a href name="concept"></a>
As it happens you may need to switch to another acl library at some time,
so i've tried to put some effort into adding an abstract layer that
would make your app more maintenaible.
This package tries to abstract your app from the acl layer
in 2 different ways:

- standard approach to route-based checks
- standard api to basic acl manipulation

In order to access permissions checks, a *permissions provider* that acts
as a bridge with the acl library must be set. Also, a *users provider*
is required in order to retrieve the authenticated user.

While the *route checks* are the main focus of this project,
the *acl manipulation* feature tries to stay out of the way so you'll just use it at will.

## Install and Setup <a href name="setup"></a>
Composer install

```
"require": [
	"foothing/laravel-wrappr": "0.*"
]
```

Add the service provider in your`config/app.php` providers array.
```php
'providers' => [
	// ...
	Foothing\Wrappr\WrapprServiceProvider::class
]

```

Then publish package configuration and migration files
```
php artisan vendor:publish --provider="Foothing\Wrappr\WrapprServiceProvider"
```

## Configure the providers <a href name="configure"></a>
Once you've published the config file you can
configure an *users provider* and a *permissions provider* accordingly
to your project setup.
This sample configuration will enable the integration with `Lock` and `Illuminate\Auth\Guard`.

In your `config/wrappr.php`
```php
'permissionsProvider' => 'Foothing\Wrappr\Lock\LockProvider',
'usersProvider' => 'Foothing\Wrappr\Providers\Users\DefaultProvider',
```

## Use within Laravel Router <a href name="route_basic"></a>
There are two use cases for this package, each implemented in
its own Middleware. Let's take a look to the default case.
First of all you need to setup the Middleware in your `App\Http\Kernel`.

Add the following line:
```php
protected $routeMiddleware = [
	'wrappr.check' => 'Foothing\Wrappr\Middleware\CheckRoute',
];
```

Use the CheckRoute Middleware to control access to your routes
like the following *routes.php*:
```php
Route::get('api/users', ['middleware:wrappr.check:admin.users', function() {
	// Access is allowed for the users with the 'admin.users' permission
}]);
```

The `CheckRoute` Middleware accepts 3 arguments:
- the required permission
- an optional resource name, i.e. 'user'
- an optional resource identifier (integer)

Example:
```php
Route::get('api/users/{id?}', ['middleware:wrappr.check:read.users,user,1', function() {
	// Access is allowed for the users with the 'read.users' permission on
	// the 'user' resource with the {id} identifier
}]);
```

Also, the Middleware can handle your route arguments. Consider the following
```php
Route::get('api/users/{id?}', ['middleware:wrappr.check:read.users,user,{id}', function() {
	// Access is allowed for the users with the 'read.users' permission on
	// the 'user' resource with the {id} identifier
}]);
```
When you pass a resource identifier within the brackets, the middleware will
try to retrieve the value from the http request automatically.

## Use with custom routes <a href name="route_custom"></a>
When you're not able to fine-control at routes definition level, there's
an alternative way of handling permissions. Think about a global
RESTful controller like the following:

```php
Route::controller('api/v1/{args?}', 'FooController');
```

Assume that your controller applies a variable pattern to handle
the routes, like for example
```php
GET /api/v1/resources/users
GET /api/v1/resources/posts
POST /api/v1/services/publish/post
```
In this case you won't be able to bind permissions with the previous method, so
the `CheckPath` middleware comes to help. In order to enable this behaviour you need
some additional setup step.

First step is to run the migration you previously published.
```
php artisan migrate
```

then you have the following two choices.

### Install routes with config file <a href name="route_install_config"></a>

You can now configure the routes you would like to put under authorization control
In your `config/wrappr.php` edit your `routes` section:
```php
'routes' => [

	[
		// Allowed values are 'get', 'post', 'put', 'delete'
		// or the '*' wildcard to enable all verbs.
		'verb' => 'post',

		// The url path we want to restrict access to.
		'path' => 'foo',

		// The required permissions for the given path.
		'permissions' => 'bar',
	],

	// This configuration will control the access to the
	// POST:api/v1/resources/users action, which will be
	// only allowed for users with the 'admin.account' permission
	[
		'verb' => 'post',
		'path' => 'api/v1/resources/users',
		'permissions' => 'admin.account',
	],


	// This configuration will control the access to the
	// PUT:api/v1/resources/posts/{id} action, which will be
	// only allowed for users with both the 'posts.create' and
	// 'posts.update' permissions on the 'post' resource with
	// the {id} identifier.
	[
		'verb' => 'put',
		'path' => 'api/v1/resources/posts/{id}',
		'permissions' => ['posts.create', 'posts.update'],
		'resource' => 'post',
	],

	// In this case the 'admin/' nested routes
	// will be granted access only when the 'admin' permission
	// is available to the current auth user.
	[
		'verb' => '*',
		'path' => 'admin/*',
		'permissions' => ['admin'],
	],

	// You can also use the path wildcard in this way,
	// therefore requiring the 'superadmin' permission
	// for each route starting with 'admin'.
	[
		'verb' => '*',
		'path' => 'admin*',
		'permissions' => ['superadmin'],
	],
],
```

Once you're done with your routes setup run the artisan command
```
php artisan wrappr:install
```

> Note that each time you change the routes configuration you should
> run the artisan command again in order to refresh them.

### Install routes programmatically <a href name="route_install_prog"></a>
Alternatively you can programmatically setup your routes using
the `RouteInstaller`. In this case you won't need the artisan command.

```php
$installer = \App::make('foothing.wrappr.installer');
$installer
	->route('get', '/api/v1/*')->requires('api.read')
	->route('put', '/api/v1/*')->requires('api.write')
	->route('delete', '/api/v1/users/{id}/*')->requires('api.write,api.read')->on('users')
	->install();

// Use wildcard
$installer->route('*', '/admin')->requires->('admin.access');
```

### Setup the middleware <a href name="middleware"></a>
Add the global Middleware to your `App\Http\Kernel` like this
```php
protected $middleware = [
	\Foothing\Wrappr\Middleware\CheckPath::class
];
```

and you're all set.

### A note on routes processing order <a href name="route_processing"></a>
The Middleware will parse all incoming http requests
to match your installed routes and it will react like the following
- if a route pattern is not found access is __granted__
- if a route pattern is found it will trigger the permissions provider
that will perform the check

Once you've got your routes installed keep in mind that
they will be processed in a hierarchical order,  from the
more specific to the more generic. Look at this example

```
api/v1/users/{id}/*
api/v1/users/{id}
api/v1/*
api/v1
api/*
```

This will result in the following behaviour
- if you request `foo/bar` route is not found hence access is allowed
- if you request `api/foo` permissions bound to the `api/*` pattern will be applied
- if you request `api/v1` permission bound to the `api/v1` pattern will be applied

and so on.

## Middleware Response <a href name="middleware_response"></a>
Both the middleware implementation will return `HTTP 401` on failure
with an additional `X-Reason: permission` header that will come handy
when dealing with responses on the client side (i.e. an angular interceptor).

If you want your error responses to be redirected when the Middleware check fails,
just set the redirect path in your **wrappr.config**

```php
'redirect' => '/login'
```
This value will be ignored when the http request is an ajax request.

## How to develop providers <a href name="providers_develop"></a>
Extend `Foothing\Wrappr\Providers\Permissions\AbstractProvider`.

You'll have the mandatory `check()` method to implement, and other optional
methods you can implement or ignore at your choice.

```php
/**
 * Check the given user has access to the given permission.
 *
 * @param      $user
 * @param      $permissions
 * @param null $resourceName
 * @param null $resourceId
 *
 * @return mixed
 */
public function check($user, $permissions, $resourceName = null, $resourceId = null);

/**
 * Check the given subject has access to the given permission.
 *
 * @param      $permissions
 * @param null $resourceName
 * @param null $resourceId
 *
 * @return mixed
 */
public function can($permissions, $resourceName = null, $resourceId = null);

/**
 * Fluent method to work on users.
 * @param $user
 * @return self
 */
public function user($user);

/**
 * Fluent method to work on roles.
 * @param $role
 * @return self
 */
public function role($role);

/**
 * Return all permissions for the given subject.
 * @return mixed
 */
public function all();

/**
 * Grant the given permissions to the given subject.
 *
 * @param      $permissions
 * @param null $resourceName
 * @param null $resourceId
 *
 * @return mixed
 */
public function grant($permissions, $resourceName = null, $resourceId = null);

/**
 * Revoke the given permissions from the given subject.
 *
 * @param      $permissions
 * @param null $resourceName
 * @param null $resourceId
 *
 * @return mixed
 */
public function revoke($permissions, $resourceName = null, $resourceId = null);
```


## License <a href name="license"></a>
[MIT](https://opensource.org/licenses/MIT)
