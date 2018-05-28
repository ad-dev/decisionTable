<?php
namespace App\Services;

use App\Contracts\FeedReaderInterface;

class CurlReader implements FeedReaderInterface
{
	private $curlHandle;

	public $errorCode;

	public $errorMsg;

	public $httpCode;

	public function read($url)
	{
		$this->curlHandle = curl_init($url);
		curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curlHandle, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->curlHandle, CURLOPT_HEADER, false);
		curl_setopt($this->curlHandle, CURLOPT_TIMEOUT_MS, 2000);
		$response = curl_exec($this->curlHandle);
		
		$this->errorCode = curl_errno($this->curlHandle);
		$this->errorMsg = curl_error($this->curlHandle);
		$this->httpCode = curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);
		
		return $response;
	}
}