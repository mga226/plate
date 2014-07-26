<?php
namespace Plate\Directive;

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
        
        if ($tag[0] == ':') {
            $isPlugin = TRUE;
            $tag = substr($tag, 1);
        }
        
        $args = explode(':', $tag);
        $tag = array_shift($args);
        
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
    
    static function parseParams($params) {
        if (preg_match_all('/(?P<param>[\w:]+)\s*=\s*(["\']?)(?P<value>.*?)(\2)/', $params, $params)) {
            $params = array_combine($params['param'], $params['value']);
        } else {
            $params = array();
        }
        
        return $params;
    }
}