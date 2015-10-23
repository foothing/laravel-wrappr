<?php namespace Foothing\Wrappr\Routes;

use Foothing\Common\Repository\Eloquent\AbstractEloquentRepository;

class RouteRepository extends AbstractEloquentRepository {

	function __construct(Route $route) {
		$this->model = $route;
	}

	/**
	 * Override default implementation so that pattern
	 * is properly trimmed before being persisted.
	 *
	 * @param $route
	 * @return Route
	 */
	function create($route) {
		$route->pattern = $this->trimPath($route->pattern);
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
		$route->pattern = $this->trimPath($route->pattern);
		return parent::update($route);
	}

	/**
	 * Find a route matching the given url path.
	 *
	 * @param $verb
	 * @param $path
	 *
	 * @return mixed
	 */
	function findByPath($verb, $path) {
		$verb = strtolower($verb);
		$route = $this->queryPatternWithRegex($verb, $path);

		if ( $route ) {
			return $route;
		}

		// We didn't found any matching route, try for wildcard.
		// Before we do it we need to remove last token. For example, when we
		// installed route 'foo/*', this query should
		//  - succeed for 'foo/1', 'foo/bar', 'foo/any/thing/you/like', etc.
		//  - fail for 'foo'
		// So, we discard and rewrite last token with the '*' wildcard.

		// Other examples
		// Installed routes
		//  'foo/*'
		//  'foo/bar'
		// In such a case, this would be the expected behaviour
		//  - 'foo' doesn't match anything
		//  - 'foo/b' matches 'foo/*'
		//  - 'foo/bar' matches 'foo/bar'
		//  - 'foo/bar/baz' doesn't match anything (wildcard here is appended at LAST position)
		else {
			$tokens = explode("/", $path);
			$tokens[count($tokens)-1] = '*';
			$path = implode("/", $tokens);
			return $this->queryPatternWithRegex($verb, $path);
		}
	}

	/**
	 * Actually perform the regexp query.
	 *
	 * @param $verb
	 * @param $path
	 * @return mixed
	 */
	protected function queryPatternWithRegex($verb, $path) {
		return $this->model
			->where('verb', $verb)
			->whereRaw("? REGEXP concat(concat('^', pattern), '$')", [ $this->trimPath($path) ])
			->first();
	}

	/**
	 * Right and left trim route from blanks and undesired characters.
	 *
	 * @param $path
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function trimPath($path) {
		// Rewrite leading double slashes.
		$path = preg_replace("/^\/\//", "/", $path);

		// If there are more double slashes route is invalid.
		if (preg_match("/\/{2,}/", $path)) {
			throw new \Exception('Route path has invalid format ' . $path);
		}

		// Trim chars.
		return trim($path, " \t\n\r\0\x0B/");
	}
}