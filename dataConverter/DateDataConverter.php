<?php

namespace Wame\RestApiModule\DataConverter;

use Nette\Utils\DateTime,
	Nette\Utils\Strings;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class DateDataConverter implements IDataConverter {

	const ISO8601 = "Y-m-d\TH:i:sO";

	/**
	 * Tells score for given type
	 * 
	 * @param string $type
	 * @return int Score
	 */
	public function scoreForType($type) {
		if (Strings::compare($type, 'date') || Strings::compare($type, 'DateTime') || Strings::compare($type, 'Nette\Utils\DateTime')) {
			return 1;
		}
	}

	/**
	 * Converts object to JSON object.
	 * 
	 * @param mixed $value
	 * @param string $type
	 * @return mixed Converted value
	 */
	public function toJson($value, $type) {
		return $value ? $value->format(self::ISO8601) : null;
	}

	/**
	 * Converts JSON object to object of given type.
	 * 
	 * @param mixed $value
	 * @param string $type
	 * @return mixed Converted value
	 */
	public function fromJson($value, $type) {
		return new DateTime($value);
	}

}
