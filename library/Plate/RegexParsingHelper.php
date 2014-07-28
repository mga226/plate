<?php
namespace Plate;

/**
 * RegexParsingHelper provides a mechanism for manipulating
 * text via preg_replace_callback(), while giving the
 * callback function access to relevant data and resources.
 *
 * @used-by \Plate\Parser::doParse()
 * 
 * @package \Plate\RegexParsingHelper
 */
class RegexParsingHelper
{
    
    /**
     * A reference to the \Plate\Parser instance that created this
     * object. Currently just used to access the parser's data.
     *
     * @var \Plate\Parser
     */
    protected $parser;
    
    public function __construct(Parser & $parser) {
        $this->parser = $parser;
    }
    
    /**
     * Create an instance of \Plate\Directive based on the contents of the
     * regex match, run the directive with the current data, and return the
     * results.
     *
     * @param array $match The match from the regular expression
     * @return string The output of the directive.
     */
    public function run(Array $match) {
        
        $directive = Directive\DirectiveFactory::create($match);
        return $directive->run($this->getData());
    }
    
    /**
     * Retrieve data from this objects embedded Parser.
     *
     * @param  string $key (optional) Key for the particular Datapoint sought.
     * @return \Plate\Dataset|\Plate\Datapoint
     */
    protected function getData() {
        return $this->parser->getData();
    }
    
    /**
     * Get a regular expression that matches template code and breaks
     * it into meaningful chunks.
     *
     * See the regex itself for some clarifying comments. Named
     * captures are used to make examining results easier in the
     * callback.
     *
     * @used-by  \Plate\RegexParsingHelper::run()
     *
     * @return string A regular expression matching any template code.
     */
    public static function regexPattern() {
        
        $regex = '
			/		
			# begin pair
			(?P<pair>' .
        
        // ==> {pairs may="have" some="params"}, contain text, and must have a closing tag, like this => {/pairs}
        '{
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
						# "single" reproduced here, because params 
						{
							(?:
								(
										(?:[^{}\ ])+
										|
										{(?:[^{}\ ])+}
		
								)
							)
							(?:\ ([^{}]|(?>{[^{}]*}))*)?
						}					
					
						# end of "single" reproduced
					))*)?
				}
				# (?P<pair_content>((?>pair) | (?>single) | [\s\S])*?)
				# replaced with the following to prevent backtracking:
				(?P<pair_content>((?>pair) | (?>single) | (?>[\s\S])*?))
				{\/\3}
			) 
			# end <pair>
			|
			# begin single
			(?P<single> # ====> {singles_look_like_this and="may" have="params"}
				{
					(?P<single_tag> # The identifying or naming string, e.g. "single_tags_look_like_this"
						(
							(?:[^{}\ ])+
							|
							{(?:[^{}\ ])+}
						)
					)
					(?P<single_params>\ ([^{}]|(?>{[^{}]*}))*)? # the params part of the string, e.g. and="may" have="params"
				}
			)
			# end <single>
			/x';
        return $regex;
    }
}

