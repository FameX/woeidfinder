<?php
namespace Famex\WoeidFinder\Adapters;



class NomatimAdapter extends AbstractAdapter implements AdapterInterface
{
	public function getDataFromLatLong($lat,$long){
		$this->_checkBeforeCall();
		$url = sprintf("http://nominatim.openstreetmap.org/reverse?format=json&lat=%s&lon=%s&accept-language=en",$lat,$long);

		$content = $this->_getContentFromUrl($url);

		return json_decode($content);

	}
}