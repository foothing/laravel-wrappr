# Laravel Wrappr

This is a Laravel 5 package that aims to simplify the process of
binding route patterns to permissions
and it is indepentent from a specific permissions handler, allowing to
add route checks even if your permissions handler
doesn't support this feature natively.

However, this package comes with [BeatSwitch/lock-laravel][bs] and Laravel
`Auth` integration out of the box.

## Install and Setup
Composer install

```
"require": [
	"foothing/laravel-wrappr": "0.*"
]
```

Add the service provider in your`config/app.php` providers section.
```php
'providers' => [
	// ...
	Foothing\Wrappr\WrapprServiceProvider::class
]

```

Then publish package configuration and migration files using
```
php artisan vendor:publish --provider="Foothing\Wrappr\WrapprServiceProvider"
```

## Configure the providers
Once you've published the config file you can
configure an *users provider* and a *permissions provider* accordingly
to your project setup. The default configuration will enable the integration with
`Lock` and `Illuminate\Auth\Guard`.

In your `config/wrappr.php`
```php
'permissionsProvider' => 'Foothing\Wrappr\Providers\Permissions\LockProvider',
'usersProvider' => 'Foothing\Wrappr\Providers\Users\DefaultProvider',
```

The former defines where the actual permission check will take place, while the latter
takes care of retrieving the Auth User.

*Note: whatever the permission handler you are using you must explicitly
add the composer dependency, then follow the vendor steps to setup and configure.*

[Lock setup can be found here][bs]

## Use within Laravel Route
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
like the following `routes.php:
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

---

## Use with custom routes
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

Run the migration you previously published.
```
php artisan migrate
```

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

Note that each time you change the routes configuration you should
run the artisan command again in order to refresh them.

---
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

Add the global Middleware to your `App\Http\Kernel` like this
```php
protected $middleware = [
	\Foothing\Wrappr\Middleware\CheckPath::class
];
```

and you're all set. The Middleware will parse all incoming http requests
to match your installed routes and it will react like the following
- if a route pattern is not found access is __granted__
- if a route pattern is found it will trigger the permissions provider
that will perform the check

### A note on routes processing order
Once you've got your routes installed keep in mind that
they will be processed in a hierarchical order,  from the
more generic to the more specific. Look at this example

```
api/*
api/v1
api/v1/*
api/v1/users/{id}
api/v1/users/{id}/*
```

This will result in the following behaviour
- if you request `foo/bar` route is not found hence access is allowed
- if you request `api/foo` permissions bound to the `api/*` pattern will be applied
- if you request `api/v1` permission bound to the `api/v1` pattern will be applied

and so on.

## Middleware Response
Both the middleware implementation will return `HTTP 401` on failure
with an additional `X-Reason: permission` header that will come handy
when dealing with responses on the client side (i.e. an angular interceptor).

If you want your error responses to be redirected when the Middleware check fails,
just set the redirect path in your **wrappr.config**

```php
'redirect' => '/login'
```

This value will be ignored when the http request is an ajax request.

## How to develop additional providers
More info coming soon.

## Roadmap
There's more i would like to implement in this package. Feel free
to drop a line if you'd like to see something implemented.

- [x] wildcard for route verb config
- [x] hierarchical routes
- [ ] routes table name configuration
- [ ] Illuminate Gate integration
- [ ] Sentry integration
- [ ] Caching

## License
[MIT](https://opensource.org/licenses/MIT)

[bs]: https://github.com/BeatSwitch/lock-laravel
