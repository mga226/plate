<?php
namespace Plate\Directive;

use \Plate\Dataset;
use \Plate\Parser;

class Conditional implements DirectiveInterface
{
    
    protected $text;
    protected $condition;
    
    function __construct($text, $condition) {
        $this->text = $text;
        $this->condition = $condition;
    }
    
    function run(Dataset $data) {
        $plate = new Parser($data);
        
        $condition = $this->condition;
        
        $code_pattern = '/\w*{\w+}\w*/';
        $replacements = array();
        
        // replace anything that looks generated with a variable reference
        preg_match_all($code_pattern, $condition, $matches);
        foreach ($matches as $i => $match) {
            
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

