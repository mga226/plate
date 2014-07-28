<?php
namespace Plate\Datapoint;

use Plate\Datapoint;

/**
 * Number Datapoint
 * TODO: roll in appropriate formatting methods.
 *
 * @package \Plate\Datapoint\Number
 */
class Number extends Datapoint
{
	/**
     * @param  mixed  $value Candidate for validation
     * @return boolean
     * @see    \Plate\Datapoint::isValidValue()
     */
    static function isValidValue($value) {
        if (is_numeric($value)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
