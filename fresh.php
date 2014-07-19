<?php

require_once('vendor/symfony/class-loader/Symfony/Component/ClassLoader/UniversalClassLoader.php');

use Symfony\Component\ClassLoader\UniversalClassLoader;
 
$loader = new UniversalClassLoader();
$loader->registerNamespace('Plate',__DIR__);
#$loader->registerNamespace("Symfony\Component" => "/path/to/symfony/components");
#$loader->registerNamespace("Monolog" => "path/to/monolog/src/");
#$loader->registerPrefix("Zend_", "path/to/zend/library");
$loader->register();


//
$plate = new Plate\Parser();
$plate->setTemplate('{title}');
$plate->setData(array('title'=>'MyArray'));
$plate->parse();
echo $plate->getBuffer();
#spl_autoload_extensions(".php");
#spl_autoload_register();
#use 





/*
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
*/