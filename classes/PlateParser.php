<?php

namespace Plate;

class RegexCallback{

	protected $plate;

	function __construct($plate){
		$this->plate = $plate;
	}

	function run($match){

		$directive = PlateDirectiveFactory::create($match);
		return $directive->run($this->getData());
	}

	public function setData($data){
		$this->plate->setData( $data );
	}

	protected function getData($key = null){
		return $this->plate->getData($key);
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

