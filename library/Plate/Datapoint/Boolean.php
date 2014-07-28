<?php
namespace Plate\Datapoint;

use Plate\Datapoint;

/**
 * Boolean Datapoint.
 * TODO: roll in appropriate formatting methods.
 *
 * @package \Plate\Datapoint\Boolean
 */
class Boolean extends Datapoint
{
    public function setValue($value) {
        return $value ? TRUE : FALSE;
    }

    /**
     * @param  mixed  $value Candidate for validation
     * @return boolean
     * @see    \Plate\Datapoint::isValidValue()
     */
    static function isValidValue($value) {
        if (is_bool($value)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
