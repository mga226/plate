<?php

namespace Plate;

interface DatapointInterface
{
	public function getValue();
	public function setValue($value);
	
	public function processForTemplate($text = NULL, $params = array());

	static function isValidValue($value);

}


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

	/* Formatting methods */

	protected function fmt_reverse($value){

		if(is_array($value)){
			return array_reverse($value);
		} else {
			return strrev((string) $value);
		}
	}

}


class Datapoint_Number extends Datapoint
{

}

class Datapoint_String extends Datapoint
{
	
	static function isValidValue($value){
		if(is_string($value)){
			return TRUE;
		} else {
			return FALSE;
		}
	}

}

class Datapoint_Boolean extends Datapoint
{
	public function setValue($value){
		return $value ? TRUE : FALSE;
	}

	static function isValidValue($value){
		if(is_bool($value)){
			return TRUE;
		} else {
			return FALSE;
		}
	}
}

class Datapoint_Dataset extends Datapoint
{
	public function setValue($value){
		
		if(is_array($value)){
			$value = new Dataset($value);
		}

		if(!($value instanceof Dataset)){
			throw new InvalidArgumentException("Data provided to setValue method must be Array or Dataset");
		}

		$this->value = $value;

	}

	public function processForTemplate($text = "", $params = array()){
		
		$plate = new Parser($this->getValue(), $text );
		$plate->parse();
		return $plate->getBuffer();

	}
}


class Datapoint_Loopable extends Datapoint{
	
	public function processForTemplate($text = "", $params = array()){
		
		$buffer = "";

		$plate = new Parser();

		foreach($this->getValue() as $item){
			$plate->setData($item);
			$plate->setTemplate($text);
			$plate->parse();
			$buffer .= $plate->getBuffer();
		}

		return $buffer;

	}
}


class DatapointFactory{
	static function create($value){
		if ($value instanceof Datapoint){
			return $value;
		} else {

			if( is_numeric($value) ){
				return new Datapoint_Number($value);
			}

			if( is_string($value) ){
				return new Datapoint_String($value);
			
			} 

			if( is_bool($value) ){
				return new Datapoint_Boolean($value);
			}

			if(is_array($value)){

				if(array_keys($value) == range(0, count($value) - 1) ){
					return new Datapoint_Loopable($value);
				} else {
					return new Datapoint_Dataset($value);
				}


			} else {
				return new Datapoint($value);
			}
		}
	}
}
