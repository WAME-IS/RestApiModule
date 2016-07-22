<?php

namespace Wame\RestApiModule\DataConverter;

use Nette\DI\Container;
use Nette\Http\FileUpload;
use Nette\InvalidArgumentException;
use Wame\RestApiModule\DataConverter\IDataConverter;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class FileUploadDataConverter implements IDataConverter
{

    /** @var Container */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Tells score for given type
     * 
     * @param string $type
     * @return int Score
     */
    public function scoreForType($type)
    {
        return $type == FileUpload::class ? 5 : 0;
    }

    /**
     * 
     * @param mixed $value
     * @return mixed Converted value
     */
    public function toJson($value, $type)
    {
        if (!$value instanceof FileUpload) {
            throw new InvalidArgumentException("Invalid argument type.");
        }
        return $value;
    }

    /**
     * 
     * @param mixed $value
     * @return mixed Converted value
     */
    public function fromJson($value, $type)
    {
        dump($value);
        if (!$value instanceof FileUpload) {
            throw new InvalidArgumentException("Invalid argument type.");
        }
        return $value;
    }
}
