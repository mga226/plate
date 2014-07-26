<?php
namespace Plate\Datapoint;

use Plate\Datapoint;

class Dataset extends Datapoint
{
    public function setValue($value) {
        
        if (is_array($value)) {
            $value = new \Plate\Dataset($value);
        }
        
        if (!($value instanceof \Plate\Dataset)) {
            throw new InvalidArgumentException("Data provided to setValue method must be Array or Dataset");
        }
        
        $this->value = $value;
    }
    
    public function processForTemplate($text = "", $params = array()) {
        
        $plate = new \Plate\Parser($this->getValue(), $text);
        $plate->parse();
        return $plate->getBuffer();
    }
}
