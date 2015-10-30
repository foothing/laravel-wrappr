<?php namespace Foothing\Wrappr\Middleware;

class CheckPath extends AbstractMiddleware {

	function handle($request, \Closure $next) {

		if ( ! $this->manager->checkPath($request->method(), $request->path()) ) {
			return $this->error($request);
		}

		return $next($request);

	}

}