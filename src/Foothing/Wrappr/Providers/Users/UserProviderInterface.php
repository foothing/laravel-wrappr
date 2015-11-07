<?php namespace Foothing\Wrappr\Providers\Users;


interface UserProviderInterface {
    function getAuthUser();
    function isSuperAdmin($user);
}