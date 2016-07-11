<?php

namespace Wame\RestApiModule\Loaders;

use Nette\Object;
use Nette\Utils\Callback;
use Nette\Utils\Strings;
use Wame\RestApiModule\Router\RestApiRoute;
use Wame\RestApiModule\Router\RestApiRouteList;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class MapRestApiLoader extends Object implements RestApiLoader {

	/** @var RestApiRouteList */
	private $restApiRouteList;

	public function __construct(RestApiRouteList $restApiRouteList) {
		$this->restApiRouteList = $restApiRouteList;
	}

	public function load() {
		return [new RestApiRoute("GET", "", Callback::closure($this, 'displayMap'))];
	}

	/**
	 * @api {get} / Display informations about API
	 */
	public function displayMap() {

		$routes = [];
		$resources = [];

		foreach ($this->restApiRouteList as $route) {

			$routes[] = self::getInfo($route);

			if ($route->getResource() && !in_array($route->getResource(), $resources)) {
				$resources[] = $route->getResource();
			}
		}

		return [
			"_resources" => $resources,
			"_links" => $routes
		];
	}
	
	/**
	 * Get displayable information from rest api route
	 * 
	 * @param RestApiRoute $route
	 * @return array
	 */
	public static function getInfo($route) {
		$info = [];
		$reflection = Callback::toReflection($route->getCallback());

		$apiAnnotation = $reflection->getAnnotation("api");
		$apiInfo = Strings::match($apiAnnotation, '~^\{(.+?)\} ([^ ]+)(.*)$~');

		$info['method'] = $route->getMethod();
		$info['_link'] = self::apiLink($apiInfo[2]);

		if ($apiInfo[3]) {
			$info['short_description'] = trim($apiInfo[3]);
		}

		$description = $reflection->getDescription();
		if ($description) {
			$info['description'] = $description;
		}
		return $info;
	}

	/**
	 * Return link for given resource
	 * 
	 * @param stirng $link
	 * @return string
	 */
	public static function apiLink($link) {
		return HOSTNAME . '/api/v1' . $link;
	}

}
