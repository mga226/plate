<?php

require_once(realpath( dirname( __FILE__ ) ).'/../classes/Datapoint.php');

class DatapointTests extends PHPUnit_Framework_Testcase
{
	
	public function testDatapointIsDatapoint(){

		$dp = new Datapoint('hello');
		$this->assertTrue($dp instanceof Datapoint);

	}

	public function testDatapointFactoryReturnsDatapoint(){

		$dp = DatapointFactory::create('hello');
		$this->assertTrue($dp instanceof Datapoint);

	}


}
