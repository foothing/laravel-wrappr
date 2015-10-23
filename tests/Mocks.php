<?php


class __ {

	public static function superadmin() {
		return \Mockery::mock('alias:Foothing\Wrappr\SuperAdminableInterface')
			->shouldReceive('isSuperAdmin')
			->andReturn(true)
			->getMock();
	}

	public static function user() {
		return \Mockery::mock('alias:Foothing\Wrappr\SuperAdminableInterface')
			->shouldReceive('isSuperAdmin')
			->andReturn(false)
			->getMock();
	}

	public static function route($resourceId = null, $resourceName = null) {
		return new \Foothing\Wrappr\Routes\Route(['resourceId' => $resourceId, 'resourceName' => $resourceName]);
	}
}