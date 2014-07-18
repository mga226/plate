<?php 

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
			



$string  = 'Hello {world this="that" that="the-other"}. My name is {name}.';

$string .= "{:plugin_name}This is in a plugin.{/:plugin_name}. This is {data} data with enclosed content {/data}.";

function callback($match){
	print_r($match);

}

$string = preg_replace_callback(PLATE_REGEX, 'callback', $string);

#preg_match_all(PLATE_REGEX, $string, $matches);
//print_r($matches);
