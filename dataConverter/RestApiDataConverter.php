<?php

namespace Wame\RestApiModule\DataConverter;

use Nette\DI\PhpReflection,
	Nette\Neon\Entity,
	Nette\Object;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class RestApiDataConverter extends Object {

	/** @var IDataConverter[] */
	private $converters = [];

	/** @var array */
	private $typeCache = [];

	/**
	 * Converts objects to JSON
	 * 
	 * @param mixed $value
	 * @param string $type
	 * @return object
	 */
	public function toJson($value, $type) {
		$converter = $this->getConverter($type);
		return $converter->toJson($value, $type);
	}

	/**
	 * Converts JSON to obejcts
	 * 
	 * @param mixed $value
	 * @param string $type
	 */
	public function fromJson($value, $type) {
		$converter = $this->getConverter($type);
		return $converter->fromJson($value, $type);
	}

	/**
	 * Get best converter for given type
	 * 
	 * @param string $type
	 * @return IDataConverter
	 */
	private function getConverter($type) {
		if (!array_key_exists($type, $this->typeCache)) {

			$bestScore = 0;
			$bestConverter = null;
			foreach ($this->converters as $converter) {
				$score = $converter->scoreForType($type);
				if ($score > $bestScore) {
					$bestScore = $score;
					$bestConverter = $converter;
				}
			}

			$this->typeCache[$type] = $bestConverter;
		}

		return $this->typeCache[$type];
	}

	public function addConverter(IDataConverter $converter) {
		$this->converters[] = $converter;
	}
	
}
