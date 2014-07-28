<?php
namespace Plate\Directive;

use \Plate\Dataset;
use \Plate\Parser;

/**
 *  Examples of template code that triggers the Conditional directive:
 *
 *    {if {somevar}} Output this text if somevar exists and is truthy. {/if}
 *
 *    {if {somevar} == "this value"} Output this text. {/if}
 *
 *    {if {somevar} == "this value" && {anothervar} !== "some other value"}
 *       The condition is true.
 *    {else}
 *       The condition is false.
 *    {/if}
 *
 *  (Could use a refector... )
 *
 *  @package \Plate\Directive\Conditional
 */
class Conditional implements DirectiveInterface
{
    
    protected $text;
    protected $condition;
    
    function __construct($text, $condition) {
        $this->text = $text;
        $this->condition = $condition;
    }
    
    /**
     * If the condition is true, parse and return the text,
     * otherwise return empty string.
     * @param  Dataset $data
     * @return string  The delimited text, parsed, or an empty string is the condition failed.
     */
    function run(Dataset $data) {
        
        $plate = new Parser($data);
        
        $condition = $this->condition;
        
        $code_pattern = '/\w*{\w+}\w*/';
        $replacements = array();
        
        /*
            For anything in the condition that looks like template code:
            - parse the template code, and save the resulting string in an array
            - replace the template code in the condition with a variable
         */
        preg_match_all($code_pattern, $condition, $matches);
        foreach ($matches as $i => $match) {
            
            // convert template code into a value
            $plate->setTemplate($match[0]);
            $plate->setData($data);         
            $plate->parse();
            
            $replacements['v' . $i] = $plate->getBuffer();
            
            $condition = preg_replace($code_pattern, '$v' . $i, $condition, 1);
        }
        
        if ($this->parseCondition($condition, $replacements)) {
            return $this->text;
        }
        
        return '';
    }
    
    protected function parseCondition($cond, $vars) {
        extract($vars);
        $valid = FALSE;
        $codeToExecute = 'if (' . $cond . ') { $valid = TRUE; } ';
        eval($codeToExecute);
        return $valid;
    }
}

