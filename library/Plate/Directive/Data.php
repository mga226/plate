<?php
namespace Plate\Directive;

/**
 * Examples of template code that trigger the Data directive:
 * 
 *    {some_datapoint}  
 *    
 *    {another_datapoint} 
 *       ...which may conained delimited text 
 *    {/another_datapoint}
 * 
 *    {one_more_datapoint which="may" have="params"}
 *
 * 
 * @uses  \Plate\Datapoint::processForTemplate()
 * @see  \Plate\Directive for more details on directives
 *
 * @package \Plate\Directive\Data
 */
class Data extends \Plate\Directive
{
    /**
     * @uses   \Plate\Datapoint::processForTemplate()
     * 
     * @param  \Plate\Dataset $data The full dataset currently living in the parser.
     * @return string               The Datapoint's output for the given template state.
     */
    public function run(\Plate\Dataset $data) {
        
        // Determing what Datapoint (or pseudodata) is being used
        $datapoint = $data->getDatapointOrPseudodata($this->getTag());

        foreach ($this->getArgs() as $format) {
            $datapoint = $datapoint->getFormatted($format);
        }
        
        // Let the datapoint decide what to return
        return $datapoint->processForTemplate($this->getText(), $this->getParams());
    }
}
