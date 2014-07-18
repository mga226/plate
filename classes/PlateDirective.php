<?php

namespace Plate;

class PlateDirectiveFactory{
	static function create(Array $regexMatch){


		if( !empty($regexMatch['pair_tag']) ){
			$tag = $regexMatch['pair_tag'];
			$params = !empty($regexMatch['pair_params']) ? $regexMatch['pair_params'] : "";
			$text = !empty($regexMatch['pair_content']) ? $regexMatch['pair_content'] : FALSE;

		} elseif ( !empty($regexMatch['single_tag']) ){
			$tag = $regexMatch['single_tag'];
			$params = !empty($regexMatch['single_params']) ? $regexMatch['single_params'] : "";
			$text = FALSE;
	
		} else {
			throw new InvalidArgumentException('No tag provided.');
		
		}

		$isPlugin = FALSE;

		if($tag[0] == ':'){
			$isPlugin = TRUE;
			$tag = substr($tag, 1);
		}

		$args = explode(':', $tag);
		$tag  = array_shift($args);


		if($tag == 'if'){
			return new PlateDirective_Conditional($text, $params);
		}


		$params = static::parseParams($params);

		if($isPlugin){
			return new PlateDirective_Plugin($tag, $params, $text, $args);
		} else {
			return new PlateDirective_Data($tag, $params, $text, $args);
		}

	}

	static function parseParams($params){
		if(preg_match_all('/(?P<param>[\w:]+)\s*=\s*(["\']?)(?P<value>.*?)(\2)/',$params, $params)){
			$params = array_combine($params['param'], $params['value']);
		} else {
			$params = array();
		}

		return $params;
	}

}


interface PlateDirectiveInterface {
		function run(Dataset $data);

}


class PlateDirective_Conditional implements PlateDirectiveInterface{

	protected $text;
	protected $condition;

	function __construct($text, $condition){
		$this->text = $text;
		$this->condition = $condition;
	}

	function run (Dataset $data){
		$plate = new Parser($data);

		$condition = $this->condition;

		$code_pattern = '/\w*{\w+}\w*/';
		$replacements = array();

		// replace anything that looks generated with a variable reference
		preg_match_all($code_pattern, $condition, $matches);
		foreach($matches as $i=>$match){
			
			$plate->setTemplate($match[0]);
			$plate->setData($data);
			$plate->parse();

			$replacements['v'.$i] = $plate->getBuffer();

			$condition = preg_replace($code_pattern, '$v'.$i, $condition, 1);
		}	

		if($this->parseCondition($condition, $replacements)){
			return $this->text;
		}

		return '';

	}

	protected function parseCondition($cond, $vars){
		extract($vars);
		$valid = FALSE;
		$codeToExecute = 'if ('.$cond.') { $valid = TRUE; } ';
		eval ( $codeToExecute );
		return $valid;
	}
}



abstract class PlateDirective implements PlateDirectiveInterface{

	private $text;
	private $tag;
	private $params;
	private $args = array();

	function __construct($tag, $params = array(), $text, $args = array()){
		$this->tag    = $tag;
		$this->params = $params;
		$this->text   = $text;
		$this->args   = $args;
	}

	abstract function run(Dataset $data);

	final function getTag(){
		return $this->tag;
	}

	final function getParams(){
		return $this->params;
	}

	final function getArgs(){
		return $this->args;
	}

	final function getText(){
		return $this->text;
	}

}

class PlateDirective_Data extends PlateDirective{

	public function run(Dataset $data){
		
		$datapoint = $data->getDatapointOrPseudodata( $this->getTag() );



		foreach($this->getArgs() as $format){

			$datapoint = $datapoint->getFormatted($format);

		}

	//echo "V: ";
	//var_dump($datapoint);

		return $datapoint->processForTemplate( 
			$this->getText(), 
			$this->getParams()
		);
	}
}

class PlateDirective_Plugin extends PlateDirective{

	public function run(Dataset $data){

		if(function_exists($this->getTag())){

			return call_user_func_array(
				$this->getTag(), 
				array( $this->getText(), $this->getParams() )
			);
		} else return FALSE;
	}

}

/*


function some_fucking_plugin($text, $params=array()){
	return "Fuck It!";
}*/