<?php
namespace Plate;

use Plate\Datapoint\DatapointInterface;
use Plate\Datapoint\DatapointFactory;
/**
 * Datapoints are Plate's most granular pieces of data. Different
 * implementations of DatapointInterface allow different types of
 * data to:
 * 
 * - intepret template code appropriately
 *   @see  ::processForTemplate()
 *   
 * - validate incoming data, ensuring it is well-formed for that type
 *   @see  ::isValidValue()
 *   
 * - have its own "formatting methods", which morph the data in 
 *   predefined ways.
 *   @see  ::getFormatMethod()
 *
 *  @package  \Plate\Datapoint
 */
class Datapoint implements DatapointInterface
{
    
    /**
     * The datapoint's value.
     * @see  \Plate\Datapoint::isValidValue() - $value must validate per this method
     * @var array $data
     */
    protected $value;
    
    /**
     * @param mixed $value Must be valid per \Plate\Datapoint::isValidValue()
     */
    function __construct($value) {
        $this->setValue($value);
    }
    
    /**
     * Determines existence of a formatting method matching $format,
     * @param  string $format  Defines which formatting method to call
     * @param  array  $params  Aspirational! This isn't used yet.
     * @return \Plate\Datapoint  The output, which will be fed back into the template parser.
     * @see  \Plate\Directive\Data::run() which calls this method
     */
    final function getFormatted($format, $params = array()) {
        
        if (!is_string($format)) {
            throw new InvalidArgumentException('$format must be a string.');
        }
        
        /*
            Calls to format methods may be chained, and on success this method
            returns a new Datapoint -- so by returning $this on failure, we are
            effectively "skipping" this format.
        */
        if (!$this->hasFormatMethod($format)) {
            return $this;
        }
        
        $formatted = $this->getFormattedValue($format);
        
        if (!($formatted instanceof Datapoint)) {
            $formatted = DatapointFactory::create($formatted);
        }
        
        return $formatted;
    }
    
    /**
     * How the datapoint processes template code. For simple data types this
     * will typically ignore the $text parameter and just return the datapoint's value
     * directly.
     *
     *  Most of the native datapoints ignore the $text param, the most obvious
     *  exception being \Plate\Datapoint\Loopable
     *
     * @param  string $text   The text contained within opening and closing tags, if any.
     * @param  array  $params Some datapoints may take an array of parameters
     * @return string         The text to replace the template code with.
     *
     * @see  \Plate\Directive\Data which calls this method
     */
    public function processForTemplate($text = NULL, $params = array()) {
        return $this->getValue($params);
    }
    
    public function getValue($params = array()) {
        return $this->value;
    }
    
    /**
     * @param mixed $value Must validate per isValidValud()
     */
    public function setValue($value) {
        if (!static ::isValidValue($value)) {
            throw new InvalidArgumentException('Invalid value provided.');
        }
        $this->value = $value;
    }
    
    /**
     * Retrieve a value for $this->getFormatted()
     * @param  string $key                    Specifies the method to use (via ::isValidValue())
     * @return mixed|\Plate\Datapoint         If not a Datapoint, will be converted to one
     * @see  \Plate\Datapoint::getFormatted() which calls this
     */
    protected function getFormattedValue($key) {
        if ($this->hasFormatMethod($key)) {
            return call_user_func_array(array($this, $this->getFormatMethod($key)), array($this->getValue()));
        }
        return FALSE;
    }
    
    static function isValidValue($value) {
        return TRUE;
    }
    
    /**
     * @param  string  $key
     * @return boolean
     */
    protected function hasFormatMethod($key) {
        return method_exists($this, $this->getFormatMethod($key));
    }
    
    /**
     * "Formatting Methods."
     *
     * Datapoint classes may have "formatting methods." The point
     * of these is to allow the transformation of a datapoint into
     * whatever display format(s) are needed.
     *
     * A very simple example is an "uppercase" format for strings,
     * but this can obviously do whatever you want.
     *
     * One real-life, example I have often used: add a method to a datapoint
     * containing a multidimensional array of data, such as performance
     * data for a mutual fund, that converts it into a format that better
     * matches how it's displayed on a website. This allows you to keep
     * the actual datapoint more pristine (read: structured the way YOU
     * want it) but adapt it to the needs of the template.
     *
     *
     * @param mixed $value The value to be formatted, typically returned
     *     from \Plate\Datapoint::getValue
     *
     * @return \Plate\Datapoint|mixed
     *
     * If the returned value is not a Datapoint, the getFormatted() method class
     * will use the DatapointFactory to convert it:
     *
     * @see  \Plate\Datapoint::getFormatted() - the method that initiates calls
     *     to formatting methods.
     * @see  \Plate\Datapoint::getFormatMethod() defines how formatting methods are named.
     *
     */

    /**
     * Defines the naming convention for "format" methods
     * @param  string $key
     * @return string The name of the method associated with $key
     */
    protected function getFormatMethod($key) {
        return 'fmt_' . $key;
    }
        
    /**
     * An example of a "format" method.
     * @param  mixed $value  The output of ::getValue()
     * @return mixed $value  The reversed value.
     */
    protected function fmt_reverse($value) {
        
        if (is_array($value)) {
            return array_reverse($value);
        } else {
            return strrev((string)$value);
        }
    }
}

