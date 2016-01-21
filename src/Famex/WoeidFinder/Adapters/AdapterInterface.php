<?php
namespace Famex\WoeidFinder\Adapters;


use Buzz\Browser;
use Illuminate\Cache\Repository;

interface AdapterInterface
{
	public function setBrowser(Browser $browser);
	public function setCache(Repository $cache);
}