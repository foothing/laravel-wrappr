<?php
class PermissionInstallerTest extends BaseTestCase {

	function _test_permission_is_prepared_for_save() {
		$this->permissionInstaller->permission('create')->to(['admins', 'editors'])->on('posts');
		$items = $this->permissionInstaller->getItems();
		$this->assertEquals(1, count($items));
		$this->assertEquals('create', $items[0]->permission);
		$this->assertEquals('admins', $items[0]->roles[0]);
		$this->assertEquals('posts', $items[0]->resource);

		$this->permissionInstaller->permission('update')->to(['admins', 'editors']);
		$this->permissionInstaller->permission('delete');

		$this->permissionInstaller->install();
		Mockery::mock('Foothing\Wrappr\Providers\Permissions\PermissionProviderInterface')->shouldReceive('grantRole');
	}

	public function tearDown() {
		Mockery::close();
	}

}