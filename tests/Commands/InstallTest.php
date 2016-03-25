<?php namespace Foothing\Wrappr\Tests\Commands;

use Foothing\Wrappr\Commands\Install;
use Foothing\Wrappr\Tests\BaseTestCase;

class InstallTest extends BaseTestCase {

    public function setUp() {
        parent::setUp();
        $this->app['config']->set('wrappr.install.routes', $this->routes());
    }

    public function testInstall() {
        $installer = \Mockery::mock('Foothing\Wrappr\Installer\RouteInstaller');
        $installer->shouldReceive('route')->twice()->andReturn($installer)
            ->shouldReceive('requires')->twice()
            ->shouldReceive('on')
            ->shouldReceive('install')->once();
        $command = new Install($installer);
        $command->handle();
    }

    protected function routes() {
        return [
            [
                'verb' => 'post',
                'path' => 'api/v1/resources/users',
                'permissions' => 'admin.account',
                'resource' => 'user',
            ],
            [
                'verb' => 'get',
                'path' => 'api/v1/resources/users',
                'permissions' => ['admin.account', 'posts.create'],
                'resource' => 'user',
            ],
       ];
    }
}
