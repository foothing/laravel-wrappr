<?php namespace Foothing\Wrappr\Permissions;

use Illuminate\Contracts\Support\Jsonable;

class Collection implements Jsonable {

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

    public function contains($permissionName, $resourceName = null, $resourceId = null) {
        $permission = new Permission($permissionName, $resourceName, $resourceId);
        foreach ($this->getAllowed() as $allowed) {
            if ($allowed->equals($permission)) {
                return true;
            }
        }
        foreach ($this->getDenied() as $allowed) {
            if ($allowed->equals($permission)) {
                return true;
            }
        }

        return false;
    }

    public function countAllowed() {
        return count($this->allowed);
    }

    public function countDenied() {
        return count($this->denied);
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     *
     * @return string
     */
    public function toJson($options = 0) {
        return json_encode([
            'allowed' => $this->getAllowed(),
            'denied' => $this->getDenied(),
        ]);
    }
}
