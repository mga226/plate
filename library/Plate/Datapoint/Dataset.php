<?php
namespace Plate\Datapoint;

use Plate\Datapoint;

/**
 * Dataset Datapoint
 * Not to be confused with \Plate\Dataset!
 *
 * Basically a wrapper for \Plate\Dataset that allows us
 * to use an instance of that class as a Datapoint.
 * 
 * TODO: roll in appropriate formatting methods.
 *
 * @package \Plate\Datapoint\Dataset
 */
class Dataset extends Datapoint
{
    /**
     * When setting values, autoconvert them to a Dataset object.
     * @param array|Dataset $value
     */
    public function setValue($value) {
        
        if (is_array($value)) {
            $value = new \Plate\Dataset($value);
        }
        
        if (!($value instanceof \Plate\Dataset)) {
            throw new InvalidArgumentException("Data provided to setValue method must be Array or Dataset");
        }
        
        $this->value = $value;
    }
    
    /**
     * \Datapoint\Dataset is one of the native Datapoint types that 
     * actually uses the $text parameter, iterating through
     * its contents and parsing the text with each new
     * set of data.
     * @param  string $text   The text to parse for each item.
     * @param  array  $params Not implemented, but should be used to filter!
     * @return string         The output buffer.
     */
    public function processForTemplate($text = "", $params = array()) {
        
        $plate = new \Plate\Parser($this->getValue(), $text);
        $plate->parse();
        return $plate->getBuffer();
    }
}
