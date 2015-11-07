<?php namespace Foothing\Wrappr\Installer;

use Foothing\Wrappr\Providers\Permissions\PermissionProviderInterface;
use Foothing\Wrappr\Routes\RouteRepository;

class RouteInstaller extends Browser {

    /**
     * @var \Foothing\Wrappr\Providers\Permissions\PermissionProviderInterface
     */
    protected $provider;

    /**
     * @var \Foothing\Wrappr\Routes\RouteRepository
     */
    protected $repository;

    /**
     * @var Parser
     */
    protected $parser;

    protected $allowed = ['GET', 'POST', 'PUT', 'DELETE'];

    function __construct(PermissionProviderInterface $provider, RouteRepository $repository) {
        $this->provider = $provider;
        $this->repository = $repository;
        $this->parser = new Parser();
        $this->reset();
    }

    public function route($verb, $pattern) {
        if ( ! in_array(strtoupper($verb), array_merge($this->allowed, ['*'])) ) {
            throw new \Exception("HTTP verb not supported");
        }

        if ( ! $pattern ) {
            throw new \Exception("Pattern can't be empty.");
        }

        /*if ($verb == '*') {
            foreach ($this->allowed as $verb) {
                $this->route($verb, $pattern);
            }
        }*/

        else {
            $this->next( $this->parser->parsePattern($pattern) )->verb = $verb;
        }

        return $this;
    }

    public function requires($permissions) {
        $this->current()->permissions = $permissions;
        return $this;
    }

    public function on($resourceName) {
        $this->current()->resourceName = $resourceName;
        return $this;
    }

    public function install() {
        if ( ! empty($this->collection) ) {
            foreach ($this->collection as $route) {
                if ($route->verb == '*') {
                    foreach ($this->allowed as $verb) {
                        $clone = $route->replicate();
                        $clone->verb = $verb;
                        $this->repository->create( $clone );
                    }
                }

                else {
                    $this->repository->create( $route );
                }
            }
        }

        /*
        if ( ! empty($this->permissions) ) {
            foreach ($this->permissions as $permission => $data) {
                if (empty($data['roles'])) {
                    return;
                }
                foreach ($data['roles'] as $role) {
                    $this->provider->grantRole($role, $permission);
                }
            }
        }
        */
        $this->reset();
    }
}