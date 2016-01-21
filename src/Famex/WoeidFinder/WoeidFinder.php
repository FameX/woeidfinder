<?php
namespace Famex\WoeidFinder;


use Famex\WoeidFinder\Adapters\NomatimAdapter;
use Famex\WoeidFinder\Adapters\OpencageAdapter;
use Famex\WoeidFinder\Adapters\YqlQueryAdapter;
use Famex\WoeidFinder\Place\Place;
use Famex\WoeidFinder\Place\PlaceInterface;
use Famex\WoeidFinder\Place\WoEID;

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

	/**
	 * @var OpencageAdapter
	 */
	protected $opencageAdapter;

	public function setNomatimAdapter(NomatimAdapter $nomatimAdapter){
		$this->nomatimAdapter = $nomatimAdapter;
	}

	public function setYqlQueryAdapter(YqlQueryAdapter $yqlQueryAdapter){
		$this->yqlQueryAdapter = $yqlQueryAdapter;
	}

	public function setOpencageAdapter(OpencageAdapter $opencageAdapter){
		$this->opencageAdapter = $opencageAdapter;
	}

	/**
	 * @param $lat
	 * @param $long
	 * @return PlaceInterface
	 */
	public function getPlace($lat, $long){
		$place = new Place();

		$nomatimData = null;
		$opencageData = null;

		$querydata = array();

		if(isset($this->nomatimAdapter)){
			$nomatimData = $this->nomatimAdapter->getDataFromLatLong($lat,$long);
			if($nomatimData){
				if(isset($nomatimData->address)){
					$address = $nomatimData->address;
					if(isset($address->country)){
						array_unshift($querydata, $address->country);
					}
					if(isset($address->state)){
						array_unshift($querydata, $address->state);
					}
					if(isset($address->city)){
						array_unshift($querydata, $address->city);
					} elseif(isset($address->town)){
						array_unshift($querydata, $address->town);
					}
				}
			}
			if(count($querydata) > 0){
				$place->setRawdataSource("nomatim");
				$place->setQueryData($querydata);
			}
		}


		if((count($querydata) < 1) && (isset($this->opencageAdapter))){
			$opencageData = $this->opencageAdapter->getDataFromLatLong($lat,$long);
			if($opencageData && isset($opencageData->results) && (count($opencageData->results) > 0)){
				if(isset($opencageData->results[0]->components)){
					$address = $opencageData->results[0]->components;
					if(isset($address->country)){
						array_unshift($querydata, $address->country);
					}
					if(isset($address->state)){
						array_unshift($querydata, $address->state);
					}
					if(isset($address->city)){
						array_unshift($querydata, $address->city);
					} elseif(isset($address->town)){
						array_unshift($querydata, $address->town);
					}

				}
			}
			if(count($querydata) > 0){
				$place->setRawdataSource("opencage");
				$place->setQueryData($querydata);
			}
		}

		if((count($querydata) < 1) || (!isset($this->yqlQueryAdapter))){
			return $place;
		}

		$yqlData = $this->yqlQueryAdapter->getDataFromQueryString(implode(", ",$querydata));

		if($yqlData && isset($yqlData->query) && isset($yqlData->query->results) && isset($yqlData->query->results->place)){
			$yqlPlace = $yqlData->query->results->place;
			if(is_array($yqlPlace)){
				$yqlPlace = $this->getProperPlace($yqlPlace);
			}
			$woeid = new WoEID();
			$woeid->woeid = $yqlPlace->woeid;
			if (isset($yqlPlace->boundingBox)) $woeid->boundingBox = $yqlPlace->boundingBox;
			if (isset($yqlPlace->centroid)) $woeid->centroid = $yqlPlace->centroid;
			if (isset($yqlPlace->placeTypeName)) $woeid->type = $yqlPlace->placeTypeName->content;
			$woeid->content = $yqlPlace->name;
			$place->setWoeid($woeid);

			unset($woeid);

			$woeid_types = array(
				'country', 'admin1', 'admin2', 'admin3', 'locality1', 'locality2', 'postal', 'timezone'
			);

			foreach ($woeid_types as $woeid_type) {
				if (isset($yqlPlace->$woeid_type)) {
					$woeid = new WoEID();
					if (isset($yqlPlace->$woeid_type->code)) $woeid->code = $yqlPlace->$woeid_type->code;
					if (isset($yqlPlace->$woeid_type->type)) $woeid->type = $yqlPlace->$woeid_type->type;
					if (isset($yqlPlace->$woeid_type->woeid)) $woeid->woeid = $yqlPlace->$woeid_type->woeid;
					if (isset($yqlPlace->$woeid_type->content)) $woeid->content = $yqlPlace->$woeid_type->content;
					$place->set($woeid_type, $woeid);
					unset($woeid);
				}
			}


		}

		return $place;

	}

	protected function getProperPlace($places){
		$okay_codes = array(7, 22, 10);
		foreach($places as $place){
			if(isset($place->placeTypeName) && isset($place->placeTypeName->code)){
				if(in_array($place->placeTypeName->code,$okay_codes)) return $place;
			}
		}
		return $places[0];
	}
}