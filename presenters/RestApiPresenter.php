<?php

namespace App\RestApiModule\Presenters;

use App\Core\Presenters\BasePresenter,
	Wame\RestApiModule\RestApiApplication;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class RestApiPresenter extends BasePresenter {

	/** @var RestApiApplication */
	private $restApiApplication;
	
	public function injectRestApiApplication(RestApiApplication $restApiApplication) {
		$this->restApiApplication = $restApiApplication;
	}
	
	public function actionDefault() {
		$response = $this->restApiApplication->request($this->getApiRequest());
		$this->sendResponse($response);
	}

	public function getApiRequest() {
		$request = $this->getParameters();
		$request['method'] = $this->getHttpRequest()->getMethod();
		return $request;
	}

}
