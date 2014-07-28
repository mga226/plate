<?php
namespace Plate\Directive;

/**
 * Examples of template code that trigger the Plugin directive:
 *
 * {:someplugin}
 * {:someplugin} Delimited text... {/:someplugin}
 * {:someplugin which="may" have="parameters..."}
 *
 * Note: the leading colon is the special sauce.
 * 
 * Basically a wrapper for pulling any sort of logic or data
 * you'd like into templates.
 *
 * TODO: implement the namespacing requirement as promised.
 *
 * @see  \Plate\Directive for more details on directives
 * 
 */
class Plugin extends Directive
{
    /**
     * @param  Dataset $data
     * @return string
     */
    public function run(Dataset $data) {
        
        if (function_exists($this->getTag())) {
            return call_user_func_array($this->getTag(), array($this->getText(), $this->getParams()));
        }
        
        return FALSE;
    }
}
