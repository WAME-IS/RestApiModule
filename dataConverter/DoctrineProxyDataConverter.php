<?php

namespace Wame\RestApiModule\DataConverter;

use Doctrine\ORM\Proxy\Proxy,
	Kdyby\Doctrine\EntityManager,
	Nette\Utils\Strings,
	Wame\RestApiModule\Loaders\MapRestApiLoader;

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

		$id = null;
		if (is_scalar($value)) {
			$id = $value;
		} elseif (is_array($value)) {
			$id = $value['id'];
		}

		$superclasses = class_parents($type);
		$entityType = $superclasses[count($superclasses) - 1];

		$entity = $this->em->getRepository($entityType)->find($id);

		if (!$entity) {
			//possible feature: if entity doesnt exist create it?
		}

		return $entity;
	}

	public function toJson($value, $type) {
		return [
			'id' => $value->id,
			'_link' => MapRestApiLoader::apiLink('/' . self::doctrineTypeToResource($type) . '/' . $value->id)
		];
	}

	/**
	 * Coverts doctrine class to (possible?) resource name
	 * 
	 * @param string $type
	 * @return string
	 */
	public static function doctrineTypeToResource($type) {
		$stype = explode("\\", $type);
		$stype = $stype[count($stype) - 1];
		if (Strings::endsWith($stype, "Entity")) {
			$stype = substr($stype, 0, -6);
		}
		return Strings::firstLower($stype);
	}

}
