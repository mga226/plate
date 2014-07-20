<?php
namespace Plate;

class DatapointFactory
{
    
    static function create($value) {
        if ($value instanceof Datapoint) {
            return $value;
        } else {
            
            if (is_numeric($value)) {
                return new Datapoint\Number($value);
            }
            
            if (is_string($value)) {
                return new Datapoint\String($value);
            }
            
            if (is_bool($value)) {
                return new Datapoint\Boolean($value);
            }
            
            if (is_array($value)) {
                
                if (array_keys($value) == range(0, count($value) - 1)) {
                    return new Datapoint\Loopable($value);
                } else {
                    return new Datapoint\Dataset($value);
                }
            } else {
                return new Datapoint($value);
            }
        }
    }
}
