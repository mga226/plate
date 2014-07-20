<?php
namespace Plate;

/**
 * Parser accepts a dataset and a template string, and parses
 * the template string using the data provided.
 *
 * This is the main point of interaction with the Plate
 * library, and the only class the MUST be directly instantiated
 * by the client.
 * 
 * @package Plate\Parser
 */
class Parser
{
    /**
     * The data to use when parsing.
     * 
     * @var \Plate\Dataset
     */
    protected $data;

    /**
     * The "current" state of the template string, which will morph as
     * parsing takes place. 
     * 
     * @var  string
     */
    protected $buffer = "";

    /**
     * Contains a method called by preg_replace_callback()
     * @see  \Plate\Parser::doParse() 
     *
     * @var  \Plate\RegexParsingHelper
     */
    protected $regexHelper;
    
    protected $iteration_count = 0;

    /**
     * Initialize the parser. Handing in a dataset and/or template string is optional.
     *
     * If $dataset is an array, it will be converted into an instance of \Plate\Dataset.
     * 
     * @param array|\Plate\Dataset $dataset
     * @param string $template
     */
    function __construct($dataset = null, $template = null) {
        
        if ($dataset) {
            $this->setData($dataset);
        }
        
        if ($template) {
            $this->setTemplate($template);
        }
        
        // set the regex helper
        $this->regexHelper = new RegexParsingHelper($this);
    }
    
    /**
     * Parse the template.
     * @return string The fully-parsed content of the template.
     */
    public function parse() {
        
    	// Parse the template repeatedly until it stops changing.
        do {
	    	$buffer = $this->getBuffer();
            $new_buffer = $this->doParse();
        } while ($new_buffer !== $buffer);

        return $this->getBuffer();

    }
    
    /**
     * Sets the template to the string provided
     * @param string $template
     */
    public function setTemplate($template) {
        
        if (!is_string($template)) {
            throw new InvalidArgumentException('Template must be a string.');
        }
        
        $this->setBuffer($template);
    }
    
    /**
     * Performs one "cycle" of parsing. Called by Parser::parse() until
     * no changes are detected.
     *
     * We use a ridiculous regular expression to capture anything that looks
     * like Plate template tags. The regex callback function examines the
     * matched content and determines what to do with it.
     *
     * @return $string The processed template.
     */
    protected function doParse() {
        
        $buffer = preg_replace_callback
        (
        	RegexParsingHelper::regexPattern(), // find anything that looks like template code
        	array($this->regexHelper, 'run'),   // ...and replace it with the appropriate output
        	$this->getBuffer()                  // ...in the current buffer
        );
        
        $this->setBuffer($buffer);

        /*
          Just for fun, we'll keep track of how many iterations it takes 
          for parsing to complete. This is guaranteed to be at least two.
          
          @see \Plate\Parser::parse() for why.
        */
		$this->iteration_count++; 

        return $buffer;
    }
    
    /**
     * Sets the data to use when parsing.
     *
     * If an array is provided, it is automatically converted into an
     * instance of \Plate\Dataset.
     *
     * @param array|\Plate\Dataset $dataset
     */
    public function setData($dataset) {
        
        if (is_array($dataset)) {
            $dataset = new Dataset($dataset);
        }
        
        if (!($dataset instanceof Dataset)) {
            throw new InvalidArgumentException("Data provided to setData method must be Array or Dataset");
        }
        
        $this->data = $dataset;
    }
    
    /**
     * Retrieve data from the template.
     *
     * @param  string $key (optional) The key for a particular datapoint.
     * @return \Plate\Datapoint|\Plate\Dataset If $key is provided,
     *     a Datapoint is returned, otherset return the full Dataset.
     */
    public function getData($key = null) {
        if ($key) {
            return $this->data->getDatapoint($key);
        } else {
            return $this->data;
        }
    }
    
    /**
     * @param string $text
     * @return void
     */
    protected function setBuffer($text) {
        $this->buffer = $text;
    }
    
    /**
     * @return string
     */
    public function getBuffer() {
        return $this->buffer;
    }
    
}

