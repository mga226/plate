<?php

namespace Plate;

class Parser{
	
	protected $data; 	// holds an instance of Dataset
	protected $buffer;	
	protected $template = '';
	protected $templateParser; // holds an instance of PlateParser

	function __construct( ArrayAccess $dataset = null, $template = null){
		if($dataset){
			$this->setData($dataset);
		}	

		if($template){
			$this->setTemplate($template);
		} 

		$this->templateParser = new RegexCallback( $this ); 

	}

	public function parse(){
	
		if($this->getBuffer())

		do {
			$buffer = $this->getBuffer();
			if(empty($buffer)){

			}
			$this->doParse();
			$new_buffer = $this->getBuffer();
		
		} while ($new_buffer !== $buffer);

	}


	public function setTemplate($template){
		if(!is_string($template))
			throw new InvalidArgumentException('Template must be a string.');
		$this->setBuffer( $template );
	}



	protected function doParse(){
		$buffer = preg_replace_callback(
				PLATE_REGEX, 
				array($this->templateParser,'run'), 
				$this->getBuffer()
		);

		$this->setBuffer($buffer);
	}

	public function setData($dataset){

		if(is_array($dataset)){
			$dataset = new Dataset($dataset);
		}

		if(!($dataset instanceof Dataset)){
			throw new InvalidArgumentException("Data provided to setData method must be Array or Dataset");
		}
		
		$this->data = $dataset;
	}

	public function getData($key = null){
		if($key){
			return $this->data->getDatapoint($key);
		} else {
			return $this->data;
		}
	}


	protected function setBuffer($text){
		$this->buffer = $text;
	}

	public function getBuffer(){
		return $this->buffer;
	}



}
