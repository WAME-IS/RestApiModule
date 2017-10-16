<?php

namespace Wame\RestApiModule\DataConverter;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class ArrayDataConverter implements IDataConverter
{
	/** @var \Nette\DI\Container */
	private $container;


	public function __construct(\Nette\DI\Container $container)
    {
		$this->container = $container;
	}


	public function scoreForType($type)
    {
		if ($type == 'array') {
			return 4;
		}

		if (class_exists($type)) {
			return is_a($type, \Traversable::class) ? 3 : 0;
		}

		if (\Nette\Utils\Strings::endsWith($type, "[]")) {
			return 3;
		}

		return 0;
	}


	public function fromJson($value, $type)
    {
		$subtype = $this->toSubtype($type);
		
		if (!$subtype) {
			throw new \Nette\InvalidArgumentException("Unknown array type \"$subtype\".");
		}
		
		$out = [];

		foreach ($value as $key => $entry) {
			$out[$key] = $this->container->getService("restApiDataConverter")->fromJson($entry, $subtype);
		}

		return $out;
	}


	public function toJson($value, $type)
    {
        if (!is_array($value)) return $value;

		$subtype = $this->toSubtype($type);
		$out = [];

		foreach ($value as $key => $entry) {
			if (!$subtype) {
				$subtype = RestApiDataConverter::findTypeByValue($entry);
			}

			$out[$key] = $this->container->getService("restApiDataConverter")->toJson($entry, $subtype);
		}

		return $out;
	}


	private function toSubtype($type)
    {
		if (\Nette\Utils\Strings::endsWith($type, "[]")) {
			return substr($type, 0, -2);
		}

		return null;
	}

}
