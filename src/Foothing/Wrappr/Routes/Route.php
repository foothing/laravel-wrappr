<?php namespace Foothing\Wrappr\Routes;

use Illuminate\Database\Eloquent\Model;

class Route extends Model {
	protected $table = "routes";
	public $timestamps = false;

	protected $fillable = ['pattern', 'resourceOffset'];

	function setVerbAttribute($verb) {
		$this->attributes['verb'] = strtolower($verb);
	}

	function setPermissionsAttribute($permissions) {
		if ( is_array($permissions) ) {
			$this->attributes['permissions'] = implode(",", $permissions);
		} else {
			$this->attributes['permissions'] = $permissions;
		}
	}

	function getPermissionsAttribute($permissions) {
		return explode(",", $permissions);
	}
}