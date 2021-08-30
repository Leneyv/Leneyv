<?php

namespace Meetanshi\DeferJS;

class Test
{

	public function execute()
	{
		$URL = 'https://dreye.me?w3_preload_css='.rand(100);
		  //set curl options
		  $curl->setOption(CURLOPT_HEADER, 0);
		  $curl->setOption(CURLOPT_TIMEOUT, 10);
		  $curl->setOption(CURLOPT_RETURNTRANSFER, true);
		  //set curl header
		  $curl->addHeader("Content-Type", "application/json");
		  //$curl->addHeader("Content-Length", 200);
		  //post request with url and data
		  $curl->post($URL, $params);
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cron.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info(__METHOD__);

		return $this;

	}
}
