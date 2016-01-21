<?php
/**
 * Created by PhpStorm.
 * User: dominik
 * Date: 21/01/16
 * Time: 12:48
 */

namespace Famex\WoeidFinder\Adapters;


class YqlQueryAdapter extends AbstractAdapter implements AdapterInterface
{
	public function getDataFromQueryString($query){
		$this->_checkBeforeCall();


		$query = sprintf("select * from geo.places where text=\"%s\"", $query);
		$yqlquery = sprintf("https://query.yahooapis.com/v1/public/yql?q=%s&format=json", urlencode($query));

		$content = $this->_getContentFromUrl($yqlquery);

		return json_decode($content);

	}
}