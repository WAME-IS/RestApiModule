<?php

namespace Wame\RestApiModule;

use Exception,
	InvalidArgumentException,
	Nette\Http\Response,
	Nette\Object,
	Nette\Utils\Callback,
	Wame\RestApiModule\Loaders\RestApiLoader,
	Wame\RestApiModule\Router\RestApiRoute,
	Wame\RestApiModule\Router\RestApiRouteList;

/**
 * Core of RestApiModule
 * 
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class RestApiApplication extends Object {

	/** @var RestApiRouteList */
	private $apiRouteList;

	public function __construct(RestApiRouteList $apiRouteList) {
		$this->apiRouteList = $apiRouteList;
	}

	/**
	 * Serve api request
	 * 
	 * @param array $request
	 * @return Response response
	 */
	public function request($request) {

		$apiRoute = $this->apiRouteList->match($request);

		if (!$apiRoute) {
			return new ApiResponse(['error' => 'resource not found'], 404);
		}

		try {
			$result = $this->callRoute($apiRoute, $request);
		} catch (Exception $e) {
			return new ApiResponse(['error' => $e->getMessage()], 500);
		}

		return new ApiResponse($result, 200);
	}

	/**
	 * 
	 * @param ApiRoute $apiRoute
	 * @param array $request
	 * @throws InvalidArgumentException
	 */
	private function callRoute(RestApiRoute $apiRoute, $request) {
		$params = [];

		foreach (Callback::toReflection($apiRoute->getCallback())->getParameters() as $parameter) {
			if (isset($request[$parameter->name])) {
				$params[] = $request[$parameter->name];
			} else {
				throw new InvalidArgumentException("Argument {$parameter->name} missing.");
			}
		}

		return call_user_func_array($apiRoute->getCallback(), $params);
	}

	/**
	 * 
	 * @param RestApiLoader $loader
	 * @return RestApiApplication
	 */
	function addRouteLoader(RestApiLoader $loader) {
		foreach ($loader->load() as $route) {
			$this->apiRouteList[] = $route;
		}
	}

}
