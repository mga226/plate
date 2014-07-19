<?php

namespace Plate\Directive;

class Data extends \Plate\Directive{

	public function run(\Plate\Dataset $data){
		
		$datapoint = $data->getDatapointOrPseudodata( $this->getTag() );



		foreach($this->getArgs() as $format){

			$datapoint = $datapoint->getFormatted($format);

		}

		return $datapoint->processForTemplate( 
			$this->getText(), 
			$this->getParams()
		);
	}
}