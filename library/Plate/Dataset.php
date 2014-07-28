<?php
namespace Plate;

/**
 * Datasets are Plate's repositories of data. They:
 * 
 * - contain an array of \Plate\Datapoint objects
 * - can have "pseudodata" methods, which act exactly like real data but 
 *       generate their value on-demand, typically by processing the 
 *       existing data somehow.
 *       - pseudodata methods take the form _pseudo_{some_string}()
 * - implement ArrayAccess so they can be manipulated like arrays.
 *
 * @package \Plate\Dataset
 */
class Dataset implements \ArrayAccess
{
    /**
     * The dataset's data, an array containing objects of type \Plate\Datapoint.
     * @var array $data
     */
    var $data;
    

    function __construct($data = array()) {
        $this->setData($data);
    }
    
    /**
     * @see \Plate\Dataset::setDatapoint - which this method calls in a loop
     * 
     * @param Array $data
     */
    function setData(Array $data) {
        foreach ($data as $key => $value) {
            $this->setDatapoint($key, $value);
        }
    }
    
    /**
     * Set a Datapoint.
     *
     * If the value provided is not a Datatype, use the DatapointFactory to convert.
     * 
     * @param string $key
     * @param mixed $value
     * @return \Plate\Datapoint
     * @throws  InvalidArgumentException If $key clashes with a "pseudodata method."
     */
    public function setDatapoint($key, $value) {
        
        if ($this->hasPseudodataMethod($key)) {
            throw new InvalidArgumentException('Cannot set data with key "' . $key . '" - clashes with pseudodata method.');
        }
        
        if (!($value instanceof Datapoint)) {
            $value = Datapoint\DatapointFactory::create($value);
        }
        
        $this->data[$key] = $value;
    }
    
    /**
     * Unset a datapoint.
     * @param  string $key
     * @return void
     * @throws InvalidArgumentException If the key references a "pseudodata method."
     */
    public function unsetDatapoint($key) {
        if ($this->hasPseudodataMethod($key)) {
            throw new InvalidArgumentException('Cannot delete key "' . $key . '" - value is calculated with pseudodata method.');
        }
        unset($this->data[$key]);
    }
    
    /**
     * Return either a datapoint or the value returned by a "pseudodata" method.
     *
     * This method allows the client (and thus a template) to treat
     * "real" data and "pseudodata" the same way, without having to
     * know which a particular key refers to.
     *
     * @param  string $key
     * @return \Plate\Datapoint
     */
    public function getDatapointOrPseudodata($key) {
        if ($this->hasDatapoint($key)) {
            return $this->getDatapoint($key);
        } elseif ($this->hasPseudodataMethod($key)) {
            return $this->pseudodata($key);
        } else {
            return FALSE;
        }
    }
    
    /**
     * @param  string $key
     * @return \Plate\Datapoint
     */
    public function getDatapoint($key) {
        return $this->data[$key];
    }
    
    /**
     * Check for the existence of a datapoint with key $key
     *
     * @param  string  $key
     * @return boolean
     */
    public function hasDatapoint($key) {
        if (isset($this->data[$key])) return TRUE;
        else return FALSE;
    }
    
    /**
     * Check for the existence of a pseudodata method with key $key
     *
     * @param  string $key
     * @return boolean
     */
    protected function hasPseudodataMethod($key) {
        
        if (method_exists($this, $this->getPseudodataMethodName($key))) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Convert a generic key into a pseudodata method name.
     *
     * If we want to change how pseudodata methods are named,
     * we only need to change this method.
     *
     * @param  string $key
     * @return string
     */
    protected function getPseudodataMethodName($key) {
        return '_pseudo_' . $key;
    }
    
    /**
     * Call the "pseudodata method" for key $key.
     *
     * If the method does not return a Datapoint, use the
     * DatapointFactory to convert.
     *
     * @param  string $key
     * @return \Plate\Datapoint
     */
    protected function pseudodata($key) {
        $val = call_user_func_array(array($this, $this->getPseudodataMethodName($key)), array());
        
        if (!($val instanceof Datapoint)) {
            $val = Datapoint\DatapointFactory::create($val);
        }
        
        return $val;
    }
    
    /**
     * ArrayAccess methods below, just providing an interface
     * so the client can interact with
     */
    
    public function offsetSet($key, $value) {
        $this->setDatapoint($key, $value);
    }
    
    public function offsetExists($key) {
        if ($this->hasDatapoint($key) || $this->hasPseudodataMethod($key)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function offsetUnset($offset) {
        $this->unsetDatapoint($offset);
    }
    public function offsetGet($offset) {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
    
    /**
     * "Pseudodata Methods."
     *
     * In addition to the actual data provided to a dataset,
     * a dataset may have _pseudo_...() methods, which generate
     * datapoints on a just-in-time basis for any data that
     * can be extracted from other data.
     *
     *	Psuedodata methods take no parameters, and return:
     *
     * @return \Plate\Datapoint|mixed
     *
     * If the returned value is not a Datapoint, the Dataset class
     * will use the DatapointFactory to convert it.
     *
     * @see  \Plate\Dataset::pseudodata() which is responsible for all
     *      calls to pseudodata methods.
     *
     */
    
    /**
     * Returns the number of fields.
     *
     * Just an example of a pseudodata method.
     *
     * @return int The number of fields in this Dataset.
     */
    public function _pseudo_numberOfFields() {
        return count($this->data);
    }
}

