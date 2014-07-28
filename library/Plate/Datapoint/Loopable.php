<?php
namespace Plate\Datapoint;

use Plate\Datapoint;

/**
 * Loopable Datapoint
 *
 * A series of like items, which the Parser will iterate through,
 * using each item as a Dataset to parse the delimited text.
 *
 * TODO: roll in appropriate formatting methods.
 *
 * @package \Plate\Datapoint\Loopable
 */
class Loopable extends Datapoint
{
    
    /**
     * Loopable is one of the native Datapoint types that
     * actually uses the $text parameter, iterating through
     * its contents and parsing the text with each new
     * set of data.
     * @param  string $text   The text to parse for each item.
     * @param  array  $params Not implemented, but should be used to filter!
     * @return string         The output buffer.
     */
    public function processForTemplate($text = "", $params = array()) {
        
        $buffer = "";
        
        $plate = new \Plate\Parser();
        
        foreach ($this->getValue() as $item) {
            $plate->setData($item); // implicitly converts $item to a Dataset object
            $plate->setTemplate($text);
            $plate->parse();
            $buffer.= $plate->getBuffer();
        }
        
        return $buffer;
    }
    
    /**
     * @param  mixed  $value
     * @return bool  TRUE if value is numeric array, otherwise FALSE
     * @see  \Plate\Datapoint::isValidValue()
     */
    static function isValidValue($value) {
        if (!is_array($value)) return FALSE;
        
        if (array_keys($value) == range(0, count($value) - 1)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
