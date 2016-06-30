<?php

namespace Wame\RestApiModule\Router;

use Nette\Utils\ArrayList,
	Nette\Utils\Callback,
	Tracy\Debugger,
	WebLoader\InvalidArgumentException;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class RestApiRouteList extends ArrayList {

	/**
	 * Match route from list of provided routes
	 * @param array $request
	 * @return ApiRoute|null
	 */
	public function match($request) {
		
		$possibleRoutes = array_filter($this->matchResource($request), function($route) use($request) {
			return $route->getMethod() == $request['method'];
		});
		
		if (!$possibleRoutes) {
			return null;
		}

		if (count($possibleRoutes) > 1) {
			usort($possibleRoutes, function($route1, $route2) use ($request) {
				$score1 = $this->routeParamScore($route1, $request);
				$score2 = $this->routeParamScore($route2, $request);
				return $score2 - $score1;
			});
		}

		return $possibleRoutes[0];
	}

	/**
	 * Find matching routes in list of provided routes, checks only resource. Can be used to get all supported methods.
	 * 
	 * @param array $request
	 * @return ApiRoute[]
	 */
	public function matchResource($request) {
		$possibleRoutes = [];

		foreach ($this as $route) {
			if ($route->getResource() == $request['apiResource']) {
				$possibleRoutes[] = $route;
			}
		}

		return $possibleRoutes;
	}

	/**
	 * 
	 * @param RestApiRoute $apiRoute
	 * @param array $request
	 * @return int
	 */
	private function routeParamScore(RestApiRoute $apiRoute, $request) {
		$score = 0;
		foreach (Callback::toReflection($apiRoute->getCallback())->getParameters() as $parameter) {
			if (isset($request[$parameter->name])) {
				$score++;
			} elseif (!$parameter->isOptional()) {
				$score -= 10;
			}
		}
		return $score;
	}

	public function offsetSet($index, $value) {
		if (!($value instanceof RestApiRoute)) {
			throw new InvalidArgumentException("Has to be instance of RestApiRoute");
		}
		parent::offsetSet($index, $value);
	}

}
