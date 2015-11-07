<?php namespace Foothing\Wrappr\Installer;

use Foothing\Wrappr\Providers\Permissions\PermissionProviderInterface;

class PermissionInstaller extends Browser {

    /**
     * @var \Foothing\Wrappr\Providers\Permissions\PermissionProviderInterface
     */
    protected $provider;

    function __construct(PermissionProviderInterface $provider) {
        $this->provider = $provider;
        $this->reset();
    }

    function permission($permission) {
        $this->next((object)['permission' => $permission, 'roles' => [], 'resource' => null]);
        return $this;
    }

    function to($roles) {
        $this->current()->roles = (array)$roles;
        return $this;
    }

    function on($resource) {
        $this->current()->resource = $resource;
        return $this;
    }

    function install() {
        if ( ! empty($this->collection) ) {
            foreach ($this->collection as $item) {
                if (empty($item->roles)) {
                    return;
                }
                foreach ($item->roles as $role) {
                    $this->provider->grantRole($role, $item->permission, $item->resource);
                }
            }
        }
    }
}