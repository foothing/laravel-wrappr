<?php namespace Foothing\Wrappr\Providers\Permissions;

abstract class AbstractProvider implements PermissionProviderInterface {

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
    abstract function check($user, $permissions, $resourceName = null, $resourceId = null);

    /**
     * Fluent method to work on users.
     * @param $user
     * @return self
     */
    function user($user) { }

    /**
     * Fluent method to work on roles.
     * @param $role
     * @return self
     */
    function role($role) { }

    /**
     * Return all permissions for the given subject.
     * @return mixed
     */
    function all() { }

    /**
     * Grant the given permissions to the given subject.
     *
     * @param      $permissions
     * @param null $resourceName
     * @param null $resourceId
     *
     * @return mixed
     */
    function grant($permissions, $resourceName = null, $resourceId = null) { }

    /**
     * Revoke the given permissions from the given subject.
     *
     * @param      $permissions
     * @param null $resourceName
     * @param null $resourceId
     *
     * @return mixed
     */
    function revoke($permissions, $resourceName = null, $resourceId = null) { }
}