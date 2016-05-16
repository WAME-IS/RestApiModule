<?php

namespace Wame\RestApiModule\Router;

use Nette\Object;

/**
 * 
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class RestApiRoute extends Object {
	
	/** @var string (GET, POST, PUT, DELTE,..) */
	private $method;
	/** @var string */
	private $resource;
	/** @var \Closure */
	private $callback;
	
	public function __construct($method, $resource, callable $callback) {
		$this->method = $method;
		$this->resource = $resource;
		$this->callback = $callback;
	}
	
	/**
	 * @return string
	 */
	function getMethod() {
		return $this->method;
	}

	/**
	 * @return string
	 */
	function getResource() {
		return $this->resource;
	}

	/**
	 * @return \Closure
	 */
	function getCallback() {
		return $this->callback;
	}
}
