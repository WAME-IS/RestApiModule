<?php

namespace Wame\RestApiModule\Vendor\Core\Router;

/**
 * Adds /api route to site router.
 * 
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class RestApiRouterItem {

	/** @var h4kuna\Gettext\GettextSetup */
	private $translator;
	
	public function __construct(\h4kuna\Gettext\GettextSetup $translator) {
		$this->translator= $translator;
	}
	
	public function setup() {
		return new \Nette\Application\Routers\Route('[<lang ' . $this->translator->routerAccept() . '>/]api/[v<apiVersion>/]<apiResource>[/<id>]', [
				'lang' => $this->translator->getDefault(),
				'module' => 'RestApi',
				'presenter' => 'RestApi',
				'apiVersion' => 1,
				'apiResource' => NULL,
				'id' => NULL
			]);
	}

}
