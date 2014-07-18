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
	
	protected $data;
	protected $buffer;
	protected $confg = array();
	protected $template;
	protected $template_parser; // this is just a little bit of trickery

	function __construct( $dataset = FALSE, $template = FALSE, $options = array()){
		if($dataset){
			$this->setData($dataset);
		}

		if($template){
			$this->setTemplate($template);
		}

		if(!empty($options)){
			$this->config($options);
		}

		$this->template_parser = new TemplateParser(); 

	}

	public function config($opt, $value=null){
		if(is_string($opt) && !is_null($value)){
			$this->setConfigItem($key, $value);
			return;
		}

		if($opt instanceof Array){
			foreach($opt as $key=>$value){
				$this->setConfigItem($key, $value);
			}
		}

		if(is_string($opt) && is_null($value))

	}

	public function setConfigItem(String $key, $value){
		$this->config[$key] = $value;
		return;
	}

	public function getConfigItem(String $key){
		if(!isset($this->config[$key])){
			throw new InvalidArgumentException("Config item $key doesn't exist.");
		}
		return $this->config[$key];
		
	}



	public function setData($dataset){

		if($dataset instanceof Array){
			$dataset = new Dataset($dataset);
		}

		if($dataset !instanceof Dataset){
			throw new InvalidArgumentException("Data provided to setData method must be Array or Dataset");
		}
		
		$this->data = $dataset;
	}

	public function getData(string $key = null){
		if($key){
			return $this->data->getDatapoint($key);
		} else {
			return $this->data;
		}
	}

	public function parse( $text = FALSE, $data = FALSE ){
	
		do {
			$buffer = $this->getBuffer();
			$this->doParse();
			$new_buffer = $this->getBuffer();
		} while ($new_buffer == $buffer)

	}

	protected function doParse(){
		$buffer = preg_replace_callback(
				PLATE_REGEX, 
				array($this->template_parser,'process'), 
				$this->getBuffer()
		);

		$this->setBuffer($buffer);
	}

	protected function setBuffer(string $text){
		$this->buffer = $text;
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

		if($templateDirective instanceof )

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




abstract class templateDirective{

	protected $matches;
	protected $buffer;

	// PROCESS returns the fully-processed 
	abstract function process(){

	}

}



class templateDirective_Array extends templateDirective{

}


class templateDirective_Data extends templateDirective{
	
	function process(){
		return $this->data->getDataOrPseudodata($this->key);
	}

}

class templateDirective_Extension extends templateDirective{
	function process(){
		
		$extension_args = array(
			
			$params
		);

		foreach($this->args as $arg){
			array_push($extension_args, $arg);
		}

		return call_user_func_array($key, array($text, $params,))
	}
}


/*
	Data sets have an array of data as an instance variable.
	These are handed to templates and function as the data for your template.

 */

class Dataset implements ArrayAccess{
	
	var $data;

	function __construct($data = array()){

		$this->setData($data);

	}

	function setData(Array $data){
		foreach($data as $key=>$value){
			$this->setDatapoint($key, $value);
		}
	}

	function setDataPoint($key, $value){
	
		if($this->hasPseudodataMethod($key)){
        	thrown new InvalidArgumentException('Cannot set data with key "'.$key.'" - clashes with pseudodata method.');
        }

		if(!($value instanceof Datapoint)){
			$value = DatapointFactory::create($value);
		}
		$this->data[$key] = $value;
	}

	function unsetDatapoint($key, $value){
		if($this->hasPseudodataMethod($key)){
        	thrown new InvalidArgumentException('Cannot delete key "'.$key.'" - value is calculated with pseudodata method.');
        }
        unset $this->data[$key];
	}

	function getDataOrPeusdodata($key){
		if($this->hasDatapoint($key){
			return $this->getDatapoint($key);

		} elseif($this->hasPseudodataMethod($key)) {
			return $this->getPseudodata($key);

		} else {
			return FALSE;

		}
	}

	function getDatapoint($key){
		return $this->data[$key];
	}

	function hasDatapoint($key){
		if(isset($this->data[$key]))
			return TRUE;
		else 
			return FALSE;
	}

	function getPseudodata($key){
		
	}

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
        	thrown new InvalidArgumentException('Cannot set data with key "'.$key.'" - clashes with pseudodata method.');
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

	function __toString(){
		return $this->getValue();
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
	public function create($value, $opts = array()){
		return new Datapoint($opts);
	}
}


