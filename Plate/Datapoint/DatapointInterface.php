<?php 

namespace Plate\Datapoint;

interface DatapointInterface
{
	public function getValue();
	public function setValue($value);	
	public function processForTemplate($text = NULL, $params = array());
	static function isValidValue($value);

}