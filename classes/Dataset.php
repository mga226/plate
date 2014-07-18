<?php

namespace Plate;

/*
	Data sets have an array of data as an instance variable.
	These are handed to templates and function as the data for your template.

 */



class Dataset implements \ArrayAccess{
	
	var $data;

	function __construct($data = array()){

		$this->setData($data);

	}

	function setData(Array $data){
		foreach($data as $key=>$value){
			$this->setDatapoint($key, $value);
		}
	}

	function setDatapoint($key, $value){
	
		if($this->hasFormatMethod($key)){
        	throw new InvalidArgumentException('Cannot set data with key "'.$key.'" - clashes with pseudodata method.');
        }

		if(!($value instanceof Datapoint)){
			$value = DatapointFactory::create($value);
		}
		$this->data[$key] = $value;
	}

	function unsetDatapoint($key, $value){
		if($this->hasFormatMethod($key)){
        	throw new InvalidArgumentException('Cannot delete key "'.$key.'" - value is calculated with pseudodata method.');
        }
        unset( $this->data[$key] );
	}

	function getDatapointOrPseudodata($key){
		if($this->hasDatapoint($key)){
			return $this->getDatapoint($key);

		} elseif($this->hasFormatMethod($key)) {
			return $this->format($key);

		} else {
			return FALSE;

		}
	}

	public function getDatapoint($key){
		return $this->data[$key];
	}

	public function hasDatapoint($key){
		if(isset($this->data[$key]))
			return TRUE;
		else 
			return FALSE;
	}

	protected function hasFormatMethod($key){
		
		if(method_exists($this, $this->getFormatMethodName($key))){
			return TRUE;
		} else {
			return FALSE;
		}
	}

	protected function getFormatMethodName($key){
		return '_fmt_'.$key;
	}

	protected function format($key){
		$val = call_user_func_array(
			array($this, $this->getFormatMethodName($key)), 
			array()
		);
		
		if(!($val instanceof Datapoint)){
			$val = DatapointFactory::create($val);
		}

		return $val;

	}


//	function getPseudodata($key){
		
//	}

	/* array access methods */
    public function offsetSet($key, $value) {
            $this->setDatapoint($key, $value);
    }
    
    public function offsetExists($key) 
    {
    	if(
    		$this->hasDatapoint($key) ||
    		$this->hasPseudodataMethod($key) 
    	){
        	return TRUE;
       	} else {
       		return FALSE;
       	}
    }
    public function offsetUnset($offset) {
        if($this->hasPseudodataMethod($key)){
        	throw new InvalidArgumentException('Cannot set data with key "'.$key.'" - clashes with pseudodata method.');
        }

        unset($this->container[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

/*
	function addDatapoint($key, $value, $options = FALSE, $class = FALSE){
		if($class){
			$dp = new DatapointFactory::create($value, $class);
		} else {
			$dp = new DatapointFactory::create($value);
		}

		if($options) {
			$dp->config($options);
		}

		$this->data[$key] = $dp;

	}

*/

	public function _fmt_numberOfFields(){
		return count($this->data);
	}

}