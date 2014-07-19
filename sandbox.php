<?php 

define('PLATE_REGEX', '
			/
		
			# will never write a regex this complicated again
			# (and will hopefully never have to revisit this one) -MGA
		
			(?P<pair>
				{
					(?P<pair_tag>
						(?:
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



$string  = 'Hello {world this="that" that="the-other"}. My name is {name}.';


class PlateCallback{

	function run($match){
		$directive = PlateDirectiveFactory::create($match);
	}
}

class PlateDirectiveFactory{
	static function create(Array $regexMatch){
	
		if( !empty($regexMatch['pair_tag']) ){
			$tag = $regexMatch['pair_tag'];
			$params = $regexMatch['pair_params'];
			$text = !empty($regexMatch['pair_content']) $regexMatch['pair_content'] : FALSE;

		} elseif ( !empty($regexMatch['single_tag']) ){
			$tag = $regexMatch['single_tag'];
			$params = $regexMatch['single_params'];
			$text = FALSE;
		} else {
			throw new InvalidArgumentException('No tag provided.');
		
		}

		$isPlugin = FALSE;

		if($tag[0] == ':'){
			$isPlugin = TRUE;
			$tag = substr($tag, 1);
		}

		$params = $this->parseParams($params);

		if($isPlugin){
			return new PlateDirective_Plugin($tag, $params, $text);
		} else {
			return new PlateDirective_Data($tag, $params, $text);
		}

	}

	protected function parseParams(String $params){
		if(preg_match_all('/(?P<param>[\w:]+)\s*=\s*(["\']?)(?P<value>.*?)(\2)/',$params, $params)){
			$params = array_combine($params['param'], $params['value']);
		} else {
			$params = array();
		}

		return $params;
	}


}


abstract class PlateDirective {

	private string $text;
	private var $tag;
	private array $params;

	function __construct($tag, $params, $text){
		$this->tag = $tag;
		$this->params = $params;
		$this->text = $text;
	}

	abstract function run();

	public function getTag(){
		return $this->tag;
	}

	public function getParams(){
		return $this->params;
	}

	public function getText(){
		return $this->text;
	}

}

class PlateDirective_Data extends PlateDirective{


	public function run($data){
		$datapoint = $data->getDataOrPseudodata($this->getTag());
		return $datapoint->processForTemplate( $this->getText(), $this->getParams() );
	}
}

class PlateDirective_Plugin extends PlateDirective{

	public function run($data, $bufferIn){
		if(function_exists($this->getTag())){
			call_user_func_array(
				$this->getTag(), 
				array( $this->getText(), $this->getParams() )
			)
		}
	}

}


/*

	WRITE THIS

*/
class PlateDirective_IfBlock extends PlateDirective{

	private string $condition;

	public function run($data){

	}


}



function callback($match){
	print_r($match);

}

$string = preg_replace_callback(PLATE_REGEX, 'callback', $string);

#preg_match_all(PLATE_REGEX, $string, $matches);
print_r($matches);