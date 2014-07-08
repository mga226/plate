<?php 

// sample usage


// create some data

$data = array(
	'title' => 'My Blog Post',
	'content' => 'Lorem ipsum dolor est...',
	'date' => '2014-06-01',
	'author' => array(
		'name' => 'Mike Acreman',
		'email' => 'hi@mikeacreman.com'
	)
);

// or you can specify the data types for each field

$data = array(
	'title' => new PlateField_Text('My Blog Post'),
	'content' => new PlateField_Text('Lorem ipsum dolor est...'),
	'date' => new PlateField_Date('2014-06-01'),
	'author' => array(
		'name' => new PlateField_String('Mike Acreman'),
		'email' => new PlateField_String('hi@mikeacreman.com')
	)
);


// create a dataset

$dataset = new Dataset($data);

$plate = new Plate();
$plate->setData($dataset);
$plate->setTemplate('blog_template.php');
$plate->parseString('');

$buffer = $plate->getBuffer();

echo $buffer; // => ""



function InstaPlate($template, $data){
	$plate = new Plate();
	$plate->setData($data);
	$plate->setTemplate($template);
	$plate->parse();
	return $plate->getBuffer();
}




class Plate{
	
	protected var $data;
	protected var $buffer;

	function __construct( $dataset = FALSE){
		if($dataset){
			$this->setData($dataset)
		}
	}

	public function setData($dataset){
		if(!($dataset is Dataset)){
			$dataset = new Dataset($data);
		}
		$this->data = $dataset;
	}

	public function getData(){
		return $this->data;
	}

	public function parse( $text = FALSE, $data = FALSE ){
	
		do {
			$buffer = $this->getBuffer();
			$this->doParse();
			$new_buffer = $this->getBuffer();
		} while ($new_buffer == $buffer)

	}

	protected function doParse(){
		$this->buffer = preg_replace_callback($this->parserCallback....);
	}




	function extractTemplateCode(){





	}


	function getBuffer(){
		return $this->buffer;
	}



}




class ParserCallback(){
	
	var $data;

	var $text;
	var $key;
	var $params;
	var $args;

	function process(){
		$templateDirective = PlateDirectiveFactory::create($matches, $this->getData());
		return $templateDirective->process();
	}

	function setData($data){
		$this->data = $data;
	}
	function getData(){
		return $this->data;
	}


}


class PlateDirectiveFactory{

	static function create($matches, $dataset){

		$pd = FALSE;

		if(...){
			$pd = new PlateDirective_Data();
		} elseif (...){
			$pd = new PlateDirective_Extension();
		} elseif(...){
			$pd = new PlateDirective_Array();
		}

		if(!$pd){
			return FALSE;
		} 

		$pd->setData($dataset);

		return $pd;

	}

}




class templateDirective{


}



class templateDirective_Array extends templateDirective{

}


class templateDirective_Data extends templateDirective{
	
	function process(){
		
		$dataPoint = $this->data->getDatapoint($this->key);

		if(
			!isset($this->args[0]) ||
			!$this->data[$key]->hasPlatelet($this->args[0]))
		) { 

			return $dataPoint->runPlatelet( $this->args[0] )

			$platelet = $this->data[$key]; 
		} else {
			$platelet = FALSE
		}

		return $dataPoint->runPlatelet($this->text, $this->params, $this->args);

	}
}

class templateDirective_Extension extends templateDirective{
	function process(){
		
		$extension_args = array(
			$text,
			$params
		);

		foreach($this->args as $arg){
			array_push($extension_args, $arg);
		}

		return call_user_func_array($key, array($text, $params,))
	}
}


class Dataset{
	
	var $data;

	function __construct($data){

		foreach($data as $key=>&$value){
			if(!($value instanceof Datapoint)){
				$value = new Datapoint($value, $key);
			}
		}
		$this->data = $data;

	}

	function getDatapoint($key){
		return $this->data[$key];
	}

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



}


class Datapoint{
	
	var $value;
	var $key;

	function __construct($value, $options = array()){
		$this->setValue = $value;
		$this->config($options);
	}

	function getString(){
		return $this->getValue();
	}

	function getValue(){
		return $this->value;
	}
	function setValue($value){
		$this->value = $value;
	}

	function config($options){
		if(empty($this->options)){
			$this->options = array();
		}
		$this->options = array_merge($this->options, $options);
	}

	function runPlatelet($string, $args){

		$method_name = $this->getPlateletName($string);

		if(method_exists($this, $method_name)){
			return call_user_func_array(
				array(
					$this,
					$method_name
				),
				$args
			);
			else return FALSE;
		}
	}

	function hasPlatelet($string){
		$method_name = $this->getPlateletName($string);
		if(method_exists($this, $method_name)){
			return TRUE;
		}
		else return FALSE;
	}

	function getPlateletName($string){
		return 'plt_'.$string;
	}

	// templating methods

	function plt_replace( $params){
		return str_ireplace($params['find'], $params['replace'], $this->getValue());
	}

	function getConfig($key){
		return isset($this->options['$key']) ? $this->options['key'] : FALSE;
	}


}

function Datapoint_string extends Datapoint{
	
}


function Datapoint_integer extends Datapoint{
	
}

function Datapoint_text extends Datapoint_string{
	
	

}

function Datapoint_date extends Datapoint{

	var $timestamp;

	var $options = array(
		'default_format' => 'Y-m-d'
	);

	function getTimestamp(){
		return strtotime($this->getValue());
	}

	function plt_format($params){
		if(empty($params['format'])){
			$format = this->getConfig('default_format');
		} else {
			$format = $params['format'];
		}
		return date($format, $this->getTimestamp());
	}

}

function Datapoint_float extends Datapoint{
	function _format(){

	}
}

function Datapoint_bool extends Datapoint{
	function getString(){
		$val = $this->getValue();
		
		if(empty($val)){
			return FALSE;
		}
		
		return TRUE;
		
	}
}


function Datapoint_array extends Datapoint{
	
}


