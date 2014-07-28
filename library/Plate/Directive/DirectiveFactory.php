<?php
namespace Plate\Directive;

/**
 * Select the appropriate Directive type and create an instance
 * for a given "match" found by the RegexParsingHelper.
 *
 * @used-by \Plate\RegexParsingHelper
 */
class DirectiveFactory
{
    static function create(Array $regexMatch) {
        
        if (!empty($regexMatch['pair_tag'])) {
            $tag = $regexMatch['pair_tag'];
            $params = !empty($regexMatch['pair_params']) ? $regexMatch['pair_params'] : "";
            $text = !empty($regexMatch['pair_content']) ? $regexMatch['pair_content'] : FALSE;
        } elseif (!empty($regexMatch['single_tag'])) {
            $tag = $regexMatch['single_tag'];
            $params = !empty($regexMatch['single_params']) ? $regexMatch['single_params'] : "";
            $text = FALSE;
        } else {
            throw new InvalidArgumentException('No tag provided.');
        }
        
        $isPlugin = FALSE;
        
        // Plugins are triggered by a leading colon.
        if ($tag[0] == ':') {
            $isPlugin = TRUE;
            $tag = substr($tag, 1);
        }
        
        /*
            Arguments are appended using colon separation like this:
            {tag:arg1:arg2:arg3}
         */ 
        $args = explode(':', $tag);
        $tag = array_shift($args);
        
        /* 
           "if" is a reserved tag that triggers the Conditional directive.
           In this case, the $params string is not really params, but a 
           condition, which the Conditional directive will know how to interpret.
        */
        if ($tag == 'if') {
            return new Conditional($text, $params);
        }
        
        $params = static ::parseParams($params);
        
        if ($isPlugin) {
            return new Plugin($tag, $params, $text, $args);
        } else {
            return new Data($tag, $params, $text, $args);
        }
    }
    
    /**
     * Convert a string like this:
     *    
     *    foo="bar" hello="world"
     *
     * ...into an array like this:
     * 
     *    array(
     *        'foo' => 'bar'
     *    )
     *
     * @param  string $params
     * @return array
     */
    static function parseParams($params) {
        if (preg_match_all('/(?P<param>[\w:]+)\s*=\s*(["\']?)(?P<value>.*?)(\2)/', $params, $params)) {
            $params = array_combine($params['param'], $params['value']);
        } else {
            $params = array();
        }
        
        return $params;
    }
}
