<?php

require ('classes/Datapoint.php');
require ('classes/Dataset.php');
require ('classes/PlateParser.php');
require ('classes/PlateDirective.php');

require ('classes/Plate.php');


$dp = DatapointFactory::create('Hello World');


$set = new Dataset(
	array(
		'title' => 'My Title',
		'content' => 'My Content!',
	)
);

//print $set->getDatapoint('content')."\n";

$plate = new Plate($set, '{numberOfFields} Title is {title}, content is {content}. {:some_fucking_plugin:reverse}. Heres something else: {title:reverse}');

$plate->parse();

echo $plate->getBuffer();
