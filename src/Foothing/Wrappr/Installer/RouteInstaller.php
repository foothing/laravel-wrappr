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

	function __construct(PermissionProviderInterface $provider, RouteRepository $repository) {
		$this->provider = $provider;
		$this->repository = $repository;
		$this->parser = new Parser();
		$this->reset();
	}

	public function route($verb, $pattern) {
		$allowed = ['GET', 'POST', 'PUT', 'DELETE'];

		if ( ! in_array(strtoupper($verb), array_merge($allowed, ['*'])) ) {
			throw new \Exception("HTTP verb not supported");
		}

		if ( ! $pattern ) {
			throw new \Exception("Pattern can't be empty.");
		}

		if ($verb == '*') {
			foreach ($allowed as $verb) {
				$this->route($verb, $pattern);
			}
		}

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
				$this->repository->create( $route );
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