<?php

namespace Wame\RestApiModule;

use Nette\Application\IResponse,
	Nette\Http\IRequest,
	Nette\Http\IResponse as IHttpResponse,
	Nette\Object,
	Nette\Utils\Callback,
	Nette\Utils\Json,
	SimpleXMLElement,
	stdClass,
	Wame\RestApiModule\Router\RestApiRoute;
use const HOSTNAME;

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

	/** @var RestApiRoute */
	private $apiRoute;

	/**
	 * @param array|stdClass payload
	 * @param string MIME content type
	 * @param RestApiRoute $apiRoute
	 */
	public function __construct($payload, $code = 200, $apiRoute = null) {
		$this->payload = $payload;
		$this->code = $code;
		$this->apiRoute = $apiRoute;
	}

	/**
	 * @return array|stdClass
	 */
	public function getPayload() {
		return $this->payload;
	}

	/**
	 * @param array|stdClass $payload
	 * @return ApiResponse
	 */
	public function setPayload($payload) {
		$this->payload = $payload;
		return $this;
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

		$accept = explode(",", $httpRequest->getHeader("Accept"))[0];

		if ($accept == "application/xml") {
			$httpResponse->setContentType('application/xml', 'utf-8');
			$this->sendXml($httpRequest, $httpResponse);
		} elseif ($accept == "text/html") {
			$httpResponse->setContentType('text/html', 'utf-8');

			echo '<pre>';
			$this->sendJson($httpRequest, $httpResponse);
			echo '</pre>';
		} else {
			$httpResponse->setContentType('application/json', 'utf-8');
			$this->sendJson($httpRequest, $httpResponse);
		}
	}

	public function sendJson(IRequest $httpRequest, IHttpResponse $httpResponse) {

		$options = 0;
		if ($httpRequest->getHeader("User-Agent")) {
			$options += Json::PRETTY;
		}

		echo Json::encode($this->getPayload(), $options);
	}

	public function sendXml(IRequest $httpRequest, IHttpResponse $httpResponse) {

		$xml = new SimpleXMLElement('<root/>');
		$xml->addAttribute("source", HOSTNAME . $httpRequest->url->path);

		echo $this->arrayToXml($this->payload, $xml)->asXML();
	}

	private function arrayToXml(array $array, SimpleXMLElement $xml) {
		foreach ($array as $k => $v) {
			is_array($v) || is_object($v) ? $this->arrayToXml((array) $v, $xml->addChild($k)) : $xml->addChild($k, $v);
		}
		return $xml;
	}

}
