<?php

namespace Wame\RestApiModule\Vendor\Core\Router;

use Wame\RouterModule\Entities\RouterEntity;

/**
 * Adds /api route to site router.
 * 
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class RestApiRouterItem {

	public function create() {
		$entity = new RouterEntity();
		$entity->route = "[<lang>/]api/[v<apiVersion>/]<apiResource>[/<id>]";
		$entity->module = "RestApi";
		$entity->presenter = "RestApi";
		$entity->action = "default";
		$entity->defaults = [
			"apiVersion" => 1,
			"apiResource" => NULL
		];
		$entity->sort = 800;
		$entity->sitemap = false;
		$entity->status = 1;
		return $entity;
	}

}
