<?php

class WoeidFinderTest extends PHPUnit_Framework_TestCase
{
	protected $woeidFinder;

	public function setup(){
		$browser = new \Buzz\Browser();
		$curl = new \Buzz\Client\Curl();
		$curl->setOption(CURLOPT_USERAGENT, "WoeidFinder/0.1");
		$browser->setClient($curl);

		$nomatimAdapter = new \Famex\WoeidFinder\Adapters\NomatimAdapter();
		$nomatimAdapter->setBrowser($browser);

		$yqlQueryAdapter = new \Famex\WoeidFinder\Adapters\YqlQueryAdapter();
		$yqlQueryAdapter->setBrowser($browser);

		$this->woeidFinder = new \Famex\WoeidFinder\WoeidFinder();
		$this->woeidFinder->setNomatimAdapter($nomatimAdapter);
		$this->woeidFinder->setYqlQueryAdapter($yqlQueryAdapter);
	}

	public function testGetPlace(){
		try {
			$place = $this->woeidFinder->getPlace(3.1578500,101.7116500);
			$this->assertInstanceOf('Famex\WoeidFinder\Place\Place',$place,"Did not return a place object");
			$this->assertInstanceOf('Famex\WoeidFinder\Place\WoEID',$place->getWoeid(),"The place object does not have a woeid object");
			$this->assertEquals('28347326',$place->getWoeid()->woeid,'The place object has a wrong woeid for this lat/long');

			$this->setExpectedException('Exception');
			$place = $this->woeidFinder->getPlace(0,0);
		} catch (Buzz\Exception\RequestException $e){
			$this->markTestSkipped(
				'Unable to connect to the YQL service.'
			);
		}

	}


}