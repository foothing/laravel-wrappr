<?php namespace Foothing\Wrappr\Providers\Permissions;


interface PermissionProviderInterface {

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

}