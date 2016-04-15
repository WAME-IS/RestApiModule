<?php

namespace Wame\RestApiModule\Loaders;

/**
 * Implementations are used to load list of routes for RestApiApplication
 * 
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
interface RestApiLoader {
	
	/**
	 * Loads list of routes for RestApiApplication
	 * 
	 * @return \Wame\RestApiModule\Router\RestApiRoute[] loaded routes
	 */
	public function load();
	
}
