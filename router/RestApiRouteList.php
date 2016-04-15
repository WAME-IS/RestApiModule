<?php

namespace Wame\RestApiModule\Router;

use Nette\Utils\ArrayList,
	WebLoader\InvalidArgumentException;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class RestApiRouteList extends ArrayList {

	/**
	 * Match route from list of provided routes
	 * @param array $request
	 * @return ApiRoute
	 */
	public function match($request) {
		foreach ($this as $route) {
			if ($route->getMethod() == $request['method'] && $route->getResource() == $request['apiResource']) {
				return $route;
			}
		}
	}

	public function offsetSet($index, $value) {
		if (!($value instanceof RestApiRoute)) {
			throw new InvalidArgumentException("Has to be instance of RestApiRoute");
		}
		parent::offsetSet($index, $value);
	}

}
