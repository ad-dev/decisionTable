<?php
namespace App\Services;

use App\Contracts\FeedReaderInterface;

class Countries
{
	private $euCountriesFeedUrl = 'https://restcountries.eu/rest/v2/regionalbloc/eu';
	
	private $cacheName = '';

	public function __construct(FeedReaderInterface $feedReader)
	{
		$this->reader = $feedReader;
		
		$this->cacheName = 'counries_' . md5($this->euCountriesFeedUrl);
	}
	
	public function fetchEUCountries()
	{ 
		$cList = cache()->get($this->cacheName, false);
		
		if (empty($cList)) {
			$res = $this->reader->read($this->euCountriesFeedUrl);
			$list = json_decode($res, true);
			foreach ($list as $item) {
				$cList[$item['alpha2Code']] = $item['name'];
			}

			cache()->forever($this->cacheName, $cList);
		}
		return $cList;
	}

	public function __toString()
	{
		return json_encode(cache()->get($this->cacheName, []));
	}
}
