<?php
namespace Plate\Datapoint;

use Plate\Datapoint;

/**
 * String Datapoint.
 * TODO: roll in appropriate formatting methods.
 *
 * @package \Plate\Datapoint\String
 */
class String extends Datapoint
{
    
    /**
     * @param  mixed  $value
     * @see  \Plate\Datapoint::isValidValue()
     */
    static function isValidValue($value) {
        if (is_string($value)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}

