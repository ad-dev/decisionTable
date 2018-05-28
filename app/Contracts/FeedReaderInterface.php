<?php
namespace App\Contracts;

interface FeedReaderInterface
{
	/**
	 * Reads feed from given URL
	 * @param string $url
	 */
	function read($url);
}