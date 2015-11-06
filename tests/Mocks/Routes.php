<?php namespace Foothing\Wrappr\Tests\Mocks;

class Routes {
    public function items() {
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