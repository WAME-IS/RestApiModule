<?php

namespace Wame\RestApiModule\DataConverter;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
interface IDataConverter {

	/**
	 * Tells score for given type
	 * 
	 * @param string $type
	 * @return int Score
	 */
	public function scoreForType($type);

	/**
	 * Converts object to JSON object.
	 * 
	 * @param mixed $value
	 * @param string $type
	 * @return mixed Converted value
	 */
	public function toJson($value, $type);

	/**
	 * Converts JSON object to object of given type.
	 * 
	 * @param mixed $value
	 * @param string $type
	 * @return mixed Converted value
	 */
	public function fromJson($value, $type);

}
