<?php

namespace Wame\RestApiModule;

use Nette\Application\IResponse,
	Nette\Http\IRequest,
	Nette\Http\IResponse as IHttpResponse,
	Nette\Object,
	Nette\Utils\Json,
	Nette\Utils\Strings,
	SimpleXMLElement,
	stdClass;

/**
 * JSON response used by REST API responses.
 * 
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class ApiResponse extends Object implements IResponse {

	/** @var array|stdClass */
	private $payload;

	/** @var int */
	private $code;

	/**
	 * @param  array|stdClass  payload
	 * @param  string    MIME content type
	 */
	public function __construct($payload, $code = 200) {
		$this->setPayload($payload);
		$this->code = $code;
	}

	/**
	 * @return array|stdClass
	 */
	public function getPayload() {
		return $this->payload;
	}

	/**
	 * Returns the code of response
	 * @return int
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * Sends response to output.
	 * @return void
	 */
	public function send(IRequest $httpRequest, IHttpResponse $httpResponse) {

		$httpResponse->setExpiration(FALSE); //TODO browser caching; ETAG
		$httpResponse->setCode($this->code);

		$accept = Strings::split($httpRequest->getHeader("Accept"), ",")[0];
		if ($accept == "application/xml") {
			$this->sendXml($httpRequest, $httpResponse);
		} else {
			$this->sendJson($httpRequest, $httpResponse);
		}
	}

	public function sendJson(IRequest $httpRequest, IHttpResponse $httpResponse) {
		$httpResponse->setContentType('application/json', 'utf-8');

		$options = 0;
		if ($httpRequest->getHeader("User-Agent")) {
			$options += Json::PRETTY;
		}

		echo Json::encode($this->payload, $options);
	}

	public function sendXml(IRequest $httpRequest, IHttpResponse $httpResponse) {
		$httpResponse->setContentType('application/xml', 'utf-8');

		$xml = new SimpleXMLElement('<root/>');
		$xml->addAttribute("source", HOSTNAME);
		array_walk_recursive($this->payload, [$xml, 'addChild']);
		
		echo $xml->asXML();
	}

}
