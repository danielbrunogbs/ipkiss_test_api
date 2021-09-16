<?php

namespace App\Exceptions;

use Exception;

class RequestException extends Exception
{
	private $response;
	private $httpCode;

	public function __construct($message, $code)
	{
		$this->response = $message;
		$this->httpCode = $code;
	}

	public function getResponse()
	{
		return $this->response;
	}

	public function getHttpCode()
	{
		return $this->httpCode;
	}
}