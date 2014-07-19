<?php

namespace Plate;

use Plate\Datapoint\DatapointInterface;

class Datapoint implements DatapointInterface
{

	protected $value;

	function __construct($value){
		$this->setValue( $value );
	}

	/*
		$params is not a thing yet, but may be in the future
	*/
	final function getFormatted($format, $params = array()){
		
		if(!is_string($format)){
			throw new InvalidArgumentException('$format must be a string.');
		}

		if(!$this->hasFormatMethod($format)){
			return $this;
		}

		$formatted = $this->getFormattedValue($format);

		if(!($formatted instanceof Datapoint)){
			$formatted = DatapointFactory::create($formatted);
		}

		return $formatted;

	}

	public function processForTemplate($text = NULL, $params = array()){
		return $this->getValue( $params );
	}
	
	public function getValue( $params=array() ){
		return $this->value;
	}
	
	public function setValue($value){
		$this->value = $value;
	}

	protected function getFormattedValue($key){
		if($this->hasFormatMethod($key)){
			return call_user_func_array(array($this, $this->getFormatMethod($key)), array($this->getValue()));
		}
		return FALSE;
	}

	static function isValidValue($value){
		return TRUE;
	}

	protected function hasFormatMethod($key){
		return method_exists($this, $this->getFormatMethod($key));
	}

	protected function getFormatMethod($key){
		return 'fmt_'.$key;
	}

	/* Example of a formatting method. */

	protected function fmt_reverse($value){

		if(is_array($value)){
			return array_reverse($value);

		} else {
			return strrev((string) $value);
			
		}
	}

}

