<?php namespace Foothing\Wrappr\Permissions;

// @TODO: toJson()
class Collection {

    /**
     * @var array
     */
    protected $allowed = [];

    /**
     * @var array
     */
    protected $denied = [];

    public function allow($permissionName, $resourceName = null, $resourceId = null) {
        $this->allowed[] = new Permission($permissionName, $resourceName, $resourceId);
    }

    public function deny($permissionName, $resourceName = null, $resourceId = null) {
        $this->denied[] = new Permission($permissionName, $resourceName, $resourceId);
    }

    public function getAllowed($index = null) {
        return $index === null ? $this->allowed : $this->allowed[ $index ];
    }

    public function getDenied($index = null) {
        return $index === null ? $this->denied : $this->denied[ $index ];
    }

    public function countAllowed() {
        return count($this->allowed);
    }

    public function countDenied() {
        return count($this->denied);
    }
}