<?php

namespace Wame\RestApiModule\DataConverter;

use Nette\DI\PhpReflection,
	Nette\InvalidArgumentException,
	Nette\Reflection\ClassType,
	stdClass,
	Wame\RestApiModule\DataConverter\IDataConverter,
	Wame\RestApiModule\DataConverter\RestApiDataConverter;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class ObjectDataConverter implements IDataConverter {

	/** @var \Nette\DI\Container */
	private $container;

	public function __construct(\Nette\DI\Container $container) {
		$this->container = $container;
	}

	/**
	 * Tells score for given type
	 * 
	 * @param string $type
	 * @return int Score
	 */
	public function scoreForType($type) {
		return class_exists($type) ? 1 : 0;
	}

	/**
	 * 
	 * @param mixed $value
	 * @return mixed Converted value
	 */
	public function toJson($value, $type) {
		if (!$value) {
			return null;
		}
		
		$object = new stdClass();

		$reflection = new ClassType($type);
		if (!$reflection->is(get_class($value))) {
			throw new InvalidArgumentException("Value $value is of wrong type (" . get_class($value) . ").");
		}

		$this->processProperties($reflection, $value, $object, 'toJson');

		return (object) $object;
	}

	/**
	 * 
	 * @param mixed $value
	 * @return mixed Converted value
	 */
	public function fromJson($value, $type) {
		if(!$value) {
			return null;
		}
		
		$reflection = new ClassType($type);

		if (!$reflection->isInstantiable()) {
			throw new InvalidArgumentException("Value $type is not instantiable type.");
		}

		$object = new $type;

		$this->processProperties($reflection, $value, $object, 'fromJson');

		return $object;
	}

	private function processProperties($reflection, $value, $object, $action) {
		foreach ($reflection->getProperties() as $property) {

			//Skip fields with @noApi annotation
			if ($property->hasAnnotation("noApi")) {
				continue;
			}

			$propertyName = $property->name;
			$var = $property->getAnnotation("var");

			if ($var) {
				$var = PhpReflection::expandClassName($var, $reflection);
			} else {
				$var = RestApiDataConverter::findTypeByValue($value->$propertyName);
			}

			if ($var) {
				$object->$propertyName = $this->container->getService("restApiDataConverter")->$action($value->$propertyName, $var);
			} else {
				$object->$propertyName = $value->$propertyName;
			}
		}
	}

}
