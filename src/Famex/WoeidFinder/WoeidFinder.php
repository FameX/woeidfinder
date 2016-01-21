<?php
namespace Famex\WoeidFinder;


use Famex\WoeidFinder\Adapters\NomatimAdapter;
use Famex\WoeidFinder\Adapters\YqlQueryAdapter;
use Famex\WoeidFinder\Place\PlaceInterface;

class WoeidFinder
{
	/**
	 * @var NomatimAdapter
	 */
	protected $nomatimAdapter;

	/**
	 * @var YqlQueryAdapter
	 */
	protected $yqlQueryAdapter;


	public function setNomatimAdapter(NomatimAdapter $nomatimAdapter){
		$this->nomatimAdapter = $nomatimAdapter;
	}

	public function setYqlQueryAdapter(YqlQueryAdapter $yqlQueryAdapter){
		$this->yqlQueryAdapter = $yqlQueryAdapter;
	}

	/**
	 * @param $lat
	 * @param $long
	 * @return PlaceInterface
	 */
	public function getPlace($lat, $long){
		$nomatimData = null;

		$querydata = array();

		if(isset($this->nomatimAdapter)){
			$nomatimData = $this->nomatimAdapter->getDataFromLatLong($lat,$long);
		}

		if($nomatimData){
			if(isset($nomatimData->address)){
				if(isset($nomatimData->address->country)){
					array_unshift($querydata,$nomatimData->address->country);
				}
				if(isset($nomatimData->address->state)){
					array_unshift($querydata,$nomatimData->address->state);
				}
				if(isset($nomatimData->address->city)){
					array_unshift($querydata,$nomatimData->address->city);
				} elseif(isset($nomatimData->address->town)){
					array_unshift($querydata,$nomatimData->address->town);
				}

				/* if(isset($nomatimData->address->city_district)){
					array_unshift($querydata,$nomatimData->address->city_district);
				}
				if(isset($nomatimData->address->suburb)){
					array_unshift($querydata,$nomatimData->address->suburb);
				} */
			}
			var_dump(implode(", ", $querydata));
			var_dump($nomatimData);
		}

	}
}