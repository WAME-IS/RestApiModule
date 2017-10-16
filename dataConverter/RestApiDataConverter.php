<?php

namespace Wame\RestApiModule\DataConverter;

use Wame\Core\Registers\BaseRegister;


/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class RestApiDataConverter extends BaseRegister
{
    public function __construct()
    {
        parent::__construct(IDataConverter::class);
    }


    /** @var array */
    private $typeCache = [];


    /**
     * Converts objects to JSON
     * 
     * @param mixed $value
     * @param string $type
     * @return object
     */
    public function toJson($value, $type = null)
    {
        if (!$type) {
            $type = self::findTypeByValue($value);
        }

        $converter = $this->getConverter($type);

        if ($converter) {
            return $converter->toJson($value, $type);
        }

        return $value;
    }


    public static function findTypeByValue($var)
    {
        if (is_object($var)) {
            return get_class($var);
        } else {
            return gettype($var);
        }
    }


    /**
     * Converts JSON to obejcts
     * 
     * @param mixed $value
     * @param string $type
     */
    public function fromJson($value, $type)
    {
        $converter = $this->getConverter($type);

        if ($converter) {
            return $converter->fromJson($value, $type);
        }

        return $value;
    }


    /**
     * Get best converter for given type
     * 
     * @param string $type
     * @return IDataConverter
     */
    private function getConverter($type)
    {
        if (!array_key_exists($type, $this->typeCache)) {

            $bestScore = 0;
            $bestConverter = null;

            foreach ($this->getAll() as $converter) {
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

}
