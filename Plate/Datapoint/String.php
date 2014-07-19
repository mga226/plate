<?php

namespace Plate\Datapoint;

use Plate\Datapoint;

class String extends Datapoint
{
	
	static function isValidValue($value){
		if(is_string($value)){
			return TRUE;
		} else {
			return FALSE;
		}
	}

}

