<?php
namespace Famex\WoeidFinder\Adapters;


class OpencageAdapter extends AbstractAdapter implements AdapterInterface
{
	protected $key;

	public function setKey($key){
		$this->key = $key;
	}

	public function getDataFromLatLong($lat,$long){
		$this->_checkBeforeCall();
		$url = sprintf("http://api.opencagedata.com/geocode/v1/json?q=%s+%s&key=%s",$lat,$long,$this->key);

		$content = $this->_getContentFromUrl($url);

		return json_decode($content);

	}
}