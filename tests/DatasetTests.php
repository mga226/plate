<?php

class DatasetTests extends PHPUnit_Framework_Testcase
{
	private $dataset;

	protected function setUp()
	{
		$this->dataset = new Dataset();
	}

	protected function tearDown()
	{
		$this->dataset = NULL;
	}

	public function testAddDatapoint()
	{
		$this->dataset->addDatapoint('title','My Blog Post');
		$datapoint_was_set = $this->dataset->hasDatapoint('title');
	}

	public function testDatapointIsDatapoint()
	{
		
	}

}
