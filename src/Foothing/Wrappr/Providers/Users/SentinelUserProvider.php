<?php namespace Foothing\Wrappr\Providers\Users;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class SentinelUserProvider extends DefaultProvider {

    function getAuthUser() {
        return Sentinel::getUser();
    }

}
