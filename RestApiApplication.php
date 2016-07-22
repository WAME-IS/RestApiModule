<?php

namespace Wame\RestApiModule;

use Exception,
	InvalidArgumentException,
	Nette\DI\PhpReflection,
	Nette\Http\Response,
	Nette\Object,
	Nette\Utils\Callback,
	Tracy\Debugger,
	Wame\RestApiModule\DataConverter\RestApiDataConverter,
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

	/** @var RestApiDataConverter */
	private $dataConverter;

	public function __construct(RestApiRouteList $apiRouteList, RestApiDataConverter $dataConverter) {
		$this->apiRouteList = $apiRouteList;
		$this->dataConverter = $dataConverter;
	}

	/**
	 * Serve api request
	 * 
	 * @param array $request
	 * @return Response response
	 */
	public function request($request) {
        
		if ($request['method'] == 'OPTIONS') {
			return $this->requestOptions($request);
		}

		$apiRoute = $this->apiRouteList->match($request);

		if (!$apiRoute) {
			return new ApiResponse(['error' => 'resource not found'], 404);
		}

		try {
			$result = $this->callRoute($apiRoute, $request);

			/*
			 * Handling of exceptions
			 * Dont display error messsages of unknow Exceptions in production mode (security reasons)
			 */
		} catch (\InvalidArgumentException $e) {
			return new ApiResponse(['error' => $e->getMessage()], 500, $apiRoute);
		} catch (\Wame\Core\Exception\RepositoryException $e) {
			return new ApiResponse(['error' => $e->getMessage()], 500, $apiRoute);
		} catch (Exception $e) {
			if (Debugger::$productionMode) {
				return new ApiResponse(['error' => "Server error"], 500, $apiRoute);
			} else {
				return new ApiResponse(['error' => $e->getMessage()], 500, $apiRoute);
			}
		}

		$result = $this->dataConverter->toJson($result);

		return new ApiResponse($result, 200, $apiRoute);
	}

	private function requestOptions($request) {
		$apiRoutes = $this->apiRouteList->matchResource($request);

		$methods = [];
		$payload = [];

		foreach ($apiRoutes as $apiRoute) {
			$methods[] = $apiRoute->getMethod();
			$payload[] = Loaders\MapRestApiLoader::getInfo($apiRoute);
		}

		$response = new ApiResponse($payload);
		$response->setHeaders(['Allow' => implode(',', array_unique($methods))]);
		return $response;
	}

	/**
	 * 
	 * @param ApiRoute $apiRoute
	 * @param array $request
	 * @throws InvalidArgumentException
	 */
	private function callRoute(RestApiRoute $apiRoute, $request) {
		$params = [];

		$callbackReflection = Callback::toReflection($apiRoute->getCallback());
		foreach ($callbackReflection->getParameters() as $parameter) {
			if (isset($request[$parameter->name])) {
				$type = PhpReflection::getParameterType($parameter);
				if ($type) {
					$params[] = $this->restApiDataConverter->fromJson($request[$parameter->name], $type);
				} else {
					$params[] = $request[$parameter->name];
				}
			} else if ($parameter->isOptional()) {
				$params[] = $parameter->getDefaultValue();
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
