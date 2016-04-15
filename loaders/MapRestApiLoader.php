<?php

namespace Wame\RestApiModule\Loaders;

use Nette\Reflection\Method,
	Nette\Utils\Callback,
	Nette\Utils\Strings,
	Wame\RestApiModule\Router\RestApiRoute,
	Wame\RestApiModule\Router\RestApiRouteList;
use const HOSTNAME;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class MapRestApiLoader extends \Nette\Object implements RestApiLoader {

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
		$resources = [];

		foreach ($this->restApiRouteList as $route) {
			if (!key_exists($route->getResource(), $resources)) {
				$resources[$route->getResource()] = [];
			}

			$reflection = Callback::toReflection($route->getCallback());

			$apiAnnotation = $reflection->getAnnotation("api");
			$apiInfo = Strings::match($apiAnnotation, '~^\{(.+?)\} (.+?) (.+)*$~');

			$info['link'] = $this->apiLink($apiInfo[2]);
			$info['short_description'] = $apiInfo[3];

			$description = $reflection->getDescription();
			if ($description) {
				$info['description'] = $description;
			}

			$resources[$route->getResource()][$route->getMethod()] = $info;
		}

		return [
			"resources" => $resources
		];
	}

	private function apiLink($link) {
		return HOSTNAME . $link;
	}

}
