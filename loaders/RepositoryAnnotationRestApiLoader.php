<?php

namespace Wame\RestApiModule\Loaders;

use Nette\DI\Container,
	Nette\Utils\Callback,
	Nette\Utils\Strings,
	Wame\Core\Repositories\BaseRepository,
	Wame\RestApiModule\Router\RestApiRoute;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class RepositoryAnnotationRestApiLoader implements RestApiLoader {

	/** @var Container */
	private $container;

	public function __construct(Container $container) {
		$this->container = $container;
	}

	public function load() {
		$routes = [];
		foreach ($this->container->findByType(BaseRepository::class) as $repoName) {
			$repository = $this->container->getService($repoName);
			$routes = array_merge($routes, $this->findAnnotations($repository));
		}
		return $routes;
	}

	private function findAnnotations($repository) {
		$routes = [];
		foreach ($repository->getReflection()->getMethods() as $method) {
			$annotation = $method->getAnnotation('api');
			if ($annotation) {
				$apiInfo = Strings::match($annotation, '~^\{(.+?)\} ([^ ]+)(.*)$~');
				$resource = Strings::trim(explode(":", $apiInfo[2])[0], "/\\"); //remove arguments and /'es
				$routes[] = new RestApiRoute(
						Strings::upper($apiInfo[1]), $resource, Callback::closure($repository, $method->getName()));
			}
		}
		return $routes;
	}

}
