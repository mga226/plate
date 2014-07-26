<?php
namespace Plate;

/**
 * A directive accepts structured information about a particular block
 * of template code, and processes it and returns a resulting string,
 * which will replace the block of template code in the parser's buffer.
 *
 * @package \Plate\Directive
 */
abstract class Directive implements Directive\DirectiveInterface
{
    
    private $text;
    private $tag;
    private $params;
    private $args = array();
    
    /**
     * @param string $tag
     * @param array  $params
     * @param string $text
     * @param array  $args
     */
    function __construct($tag, $params = array(), $text, $args = array()) {
        $this->tag = $tag;
        $this->params = $params;
        $this->text = $text;
        $this->args = $args;
    }
    
    /**
     * Do...whatever the concrete directive does, returning a string that will 
     * replace the block of text that is waiting for a result.
     *
     * For concrete directives (inheriting this class), this should be the only 
     * method overriden.
     *
     * @param  \Plate\Dataset $data
     * @return string
     */
    abstract function run(\Plate\Dataset $data);
    
    // some getters!
    
    final function getTag() {
        return $this->tag;
    }
    
    final function getParams() {
        return $this->params;
    }
    
    final function getArgs() {
        return $this->args;
    }
    
    final function getText() {
        return $this->text;
    }
}
