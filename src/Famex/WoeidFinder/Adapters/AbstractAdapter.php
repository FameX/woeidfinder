<?php
namespace Famex\WoeidFinder\Adapters;

use Buzz\Browser;
use Illuminate\Cache\Repository;


abstract class AbstractAdapter implements AdapterInterface
{
	protected $browser;
	protected $cache;


	public function setBrowser(Browser $browser)
	{
		$this->browser = $browser;
	}

	public function setCache(Repository $cache)
	{
		$this->cache = $cache;
	}

	protected function _getContentFromUrl($url){
		$key = "WoeidFinder-".md5($url);

		if (($this->cache != false) && ($content = $this->cache->get($key))) {
			return $content;
		}

		try{
			$result = $this->browser->get($url);
		} catch (RequestException $e){
			throw new AdapterException("Unable to connect to the service.",500,$e);
		}
		if(!$result->isOk()){
			throw new AdapterException($result->getReasonPhrase(),$result->getStatusCode());
		}
		$content = $result->getContent();

		if ($this->cache != false) {
			$this->cache->put($key, $content, 60);
		}

		return $content;

	}

	protected function _checkBeforeCall(){
		if(!isset($this->browser)){
			throw new AdapterException("No browser set");
		}
	}

}