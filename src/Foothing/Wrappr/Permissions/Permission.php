<?php namespace Foothing\Wrappr\Permissions;

class Permission {
    public $name;
    public $resourceName;
    public $resourceId;
    public $inherited;

    public function __construct($name, $resourceName = null, $resourceId = null) {
        $this->name = $name;
        $this->resourceName = $resourceName;
        $this->resourceId = $resourceId;
    }
}
