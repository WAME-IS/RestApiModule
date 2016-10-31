<?php

namespace Wame\RestApiModule\Vendor\Core\Registers;

use Wame\RouterModule\Entities\RouterEntity;

class RestApiRouterEntity {

	public static function create() {
		$entity = new RouterEntity();
		$entity->route = "[<lang>/]api/[v<apiVersion>/]<apiResource>";
		$entity->module = "RestApi";
		$entity->presenter = "RestApi";
		$entity->action = "default";
		$entity->defaults = [
			"apiVersion" => 1,
			"apiResource" => NULL
		];
		$entity->sort = -1;
		$entity->sitemap = false;
		$entity->status = 1;
		return $entity;
	}

}
