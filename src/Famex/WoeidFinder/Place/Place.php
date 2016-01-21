<?php
namespace Famex\WoeidFinder\Place;


class Place implements PlaceInterface
{
	protected $rawdataSource;
	protected $queryData = array();
	protected $woeid;

	/**
	 * @param string $type
	 * @param WoEID $woeid
	 */
	public function set($type,$woeid){
		$this->$type = $woeid;
	}


	/**
	 * @return WoEID
	 */
	public function getWoeid()
	{
		return $this->woeid;
	}

	/**
	 * @param WoEID $woeid
	 */
	public function setWoeid($woeid)
	{
		$this->woeid = $woeid;
	}

	/**
	 * @return array
	 */
	public function getQueryData()
	{
		return $this->queryData;
	}

	/**
	 * @param array $queryData
	 */
	public function setQueryData($queryData)
	{
		$this->queryData = $queryData;
	}

	/**
	 * @return string
	 */
	public function getRawdataSource()
	{
		return $this->rawdataSource;
	}

	/**
	 * @param string $rawdataSource
	 */
	public function setRawdataSource($rawdataSource)
	{
		$this->rawdataSource = $rawdataSource;
	}
}