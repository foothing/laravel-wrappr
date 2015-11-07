<?php namespace Foothing\Wrappr\Routes;

use Foothing\Repository\Eloquent\EloquentRepository;
use Foothing\Wrappr\Installer\Parser;

class RouteRepository extends EloquentRepository {
    /**
     * @var \Foothing\Wrappr\Installer\Parser
     */
    protected $parser;

    function __construct(Route $route, Parser $parser) {
        parent::__construct($route);
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

    /**
     * Return routes by verb, ordered by pattern desc.
     *
     * @param $verb
     * @return mixed
     */
    public function getOrderedRoutes($verb) {
        return $this->filter('verb', $verb)->order('pattern', 'desc')->all();
    }

}