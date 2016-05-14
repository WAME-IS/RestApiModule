<?php

namespace Wame\RestApiModule\DataConverter;

use Doctrine\ORM\Proxy\Proxy,
	Kdyby\Doctrine\EntityManager,
	Nette\Utils\Strings;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class DoctrineProxyDataConverter implements IDataConverter {

	/** @var EntityManager */
	private $em;

	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	public function scoreForType($type) {
		return Strings::contains($type, Proxy::MARKER) ? 3 : 0;
	}

	public function fromJson($value, $type) {
		
		if(!is_scalar($value)) {
			throw new \Nette\InvalidArgumentException("This field has to be only ID.");
		}
		
		$superclasses = class_parents($type);
		$entityType = $superclasses[count($superclasses) - 1];
		
		return $this->em->getRepository($entityType)->find($value);
		
		//possible feature: if entity doesnt exist create it?
	}

	public function toJson($value, $type) {
		return ['id' => $value->id];
	}

}
