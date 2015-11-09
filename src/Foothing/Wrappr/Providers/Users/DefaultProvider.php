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
        // Expect user to implement the isSuperAdmin() method.
        if (method_exists($user, 'isSuperAdmin')) {
            return $user->isSuperAdmin();
        }

        throw new \Exception("User implementation doesn't provide isSuperAdmin");
    }
}