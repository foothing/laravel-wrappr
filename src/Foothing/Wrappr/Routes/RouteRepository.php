<?php namespace Foothing\Wrappr\Routes;

use Foothing\Common\Repository\Eloquent\AbstractEloquentRepository;
use Foothing\Wrappr\Installer\Parser;

class RouteRepository extends AbstractEloquentRepository {
	/**
	 * @var \Foothing\Wrappr\Installer\Parser
	 */
	protected $parser;

	function __construct(Route $route, Parser $parser) {
		$this->model = $route;
		$this->parser = $parser;
	}

	/**
	 * Override default implementation so that pattern
	 * is properly trimmed before being persisted.
	 *
	 * @param $route
	 * @return Route
	 */
	function create($route) {
		$route->pattern = $this->parser->trimPath($route->pattern);
		return parent::create($route);
	}

	/**
	 * Override default implementation so that pattern
	 * is properly trimmed before being persisted.
	 *
	 * @param $route
	 * @return Route
	 */
	function update($route) {
		$route->pattern = $this->parser->trimPath($route->pattern);
		return parent::update($route);
	}

}