<?php namespace Foothing\Wrappr\Tests\Mocks;

class Mocks {

    public static function superadmin() {
        return \Mockery::mock('alias:Foothing\Wrappr\SuperAdminableInterface')
            ->shouldReceive('isSuperAdmin')
            ->andReturn(true)
            ->getMock();
    }

    public static function user() {
        return (object)['id' => 1];
    }

    public static function route($resourceId = null, $resourceName = null) {
        return new \Foothing\Wrappr\Routes\Route(['resourceId' => $resourceId, 'resourceName' => $resourceName]);
    }

    public static function routes() {
        $parser = new \Foothing\Wrappr\Installer\Parser();
        return [
            $parser->parsePattern("api/v1/users/{id}/*"),
            $parser->parsePattern("api/v1/users/{id}"),
            $parser->parsePattern("api/v1/*"),
            $parser->parsePattern("api/v1"),
            $parser->parsePattern("api/*"),
            $parser->parsePattern("*"),
        ];
    }
}