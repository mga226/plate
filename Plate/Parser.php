<?php

namespace Plate;

class Parser{
	
	protected $data;          // holds an instance of Dataset
	protected $buffer;        // holds current content as it morphs during the parse
	protected $regexCallback; // holds an instance of \Plate\RegexCallback

	function __construct( \ArrayAccess $dataset = null, $template = null){
		
		if($dataset){
			$this->setData($dataset);
		}	

		if($template){
			$this->setTemplate($template);
		} 

		// set the 
		$this->regexCallback = new RegexCallback( $this ); 

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
		
		if(!is_string($template)){
			throw new InvalidArgumentException('Template must be a string.');
		}

		$this->setBuffer( $template );
	}



	protected function doParse(){
		
		$buffer = preg_replace_callback(
				PLATE_REGEX, 
				array($this->regexCallback,'run'), 
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



			define('PLATE_REGEX', '
			/
		
			# will never write a regex this complicated again
			# (and will hopefully never have to revisit this one) -MGA
		
			(?P<pair>
				{
					(?P<pair_tag>
						(
							(?>
								(?:[^{}\ ])+
								|
								{(?:[^{}\ ])+}
							)+	
						)
					)
					(?P<pair_params>\ ([^{}]|(?:
					
					
						# single reproduced here
					
						{
							(?:
								(
									#(?>
										(?:[^{}\ ])+
										|
										{(?:[^{}\ ])+}
									#)+
		
								)
							)
							(?:\ ([^{}]|(?>{[^{}]*}))*)?
						}					
					
						# end of single reproduced
					
					))*)?
				}
				# (?P<pair_content>((?>pair) | (?>single) | [\s\S])*?)
				# replaced with the following to circumvent catastrophic backtracking:
				(?P<pair_content>((?>pair) | (?>single) | (?>[\s\S])*?))
				{\/\3}
			)
			|
			(?P<single>
				{
					(?P<single_tag>
						(
							#(?>
								(?:[^{}\ ])+
								|
								{(?:[^{}\ ])+}
							#)+	

						)
					)
					(?P<single_params>\ ([^{}]|(?>{[^{}]*}))*)?
				}
			)
			
		
			/x');
			