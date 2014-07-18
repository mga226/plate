<?php

namespace Plate;

require_once(realpath( dirname( __FILE__ ) ).'/../classes/Plate.php');
require_once(realpath( dirname( __FILE__ ) ).'/../classes/Dataset.php');
require_once(realpath( dirname( __FILE__ ) ).'/../classes/Datapoint.php');
require_once(realpath( dirname( __FILE__ ) ).'/../classes/PlateDirective.php');
require_once(realpath( dirname( __FILE__ ) ).'/../classes/PlateParser.php');



class PlateTests extends \PHPUnit_Framework_Testcase
{

	var $plate;

	public function setUp(){
		$this->plate = new Parser();
	}
	
	public function tearDown(){
		unset( $this->plate );
	}

	public function testSettingDataWithArrayCreatesDataset()
	{

		$this->plate->setData(array('text'=>'Hello, World!'));
		$this->assertTrue($this->plate->getData() instanceof Dataset);
		
	}

	public function testSettingTemplateSetsBuffer()
	{
		$template = '{text}';
		$this->plate->setTemplate($template);
		$this->assertEquals( $this->plate->getBuffer(), $template); 

	}

	public function testParsingDataWorks()
	{
		

		$this->plate->setTemplate('{text}');
		$this->plate->setData(array('text'=>'Hello, World!'));

		$this->plate->parse();

		$this->assertEquals( $this->plate->getBuffer(), 'Hello, World!'); 

	}

	public function testParsingPseudodataWorks()
	{

		$this->plate->setTemplate('{numberOfFields}');
		$this->plate->setData(array('text'=>'Hello, World!'));

		$this->plate->parse();

		$expectedResult = $this->plate->getData()->getDatapointOrPseudodata('numberOfFields')->getValue();

		$this->assertEquals( $this->plate->getBuffer(), $expectedResult); 

	}



	public function testParsingFormatMethodWorks()
	{
		$this->plate->setTemplate('{text:reverse}');
		$this->plate->setData(array('text'=>'hello!'));

		$this->plate->parse();

		$expectedResult = '!olleh';

		$this->assertEquals( $this->plate->getBuffer(), $expectedResult); 

	}

	public function testBoolReturnsBoolDatapoint()
	{

		$dp = DatapointFactory::create(TRUE);
		$this->assertInstanceOf('Datapoint_Boolean', $dp);

	}

	public function testnumberReturnsNumberDatapoint()
	{

		$dp = DatapointFactory::create(123);
		$this->assertInstanceOf('Datapoint_Number', $dp);

		$dp = DatapointFactory::create('123');
		$this->assertInstanceOf('Datapoint_Number', $dp);

		$dp = DatapointFactory::create(123.4);
		$this->assertInstanceOf('Datapoint_Number', $dp);


		$dp = DatapointFactory::create('123.4');
		$this->assertInstanceOf('Datapoint_Number', $dp);

		$dp = DatapointFactory::create(0);
		$this->assertInstanceOf('Datapoint_Number', $dp);


	}

	public function testStringReturnsStringDatapoint()
	{

		$dp = DatapointFactory::create('hello world');
		$this->assertInstanceOf('Datapoint_String', $dp);

	}

	public function testAssociativeArrayReturnsDatasetDatapoint()
	{

		$dp = array(
			'title' => 'My Title',
			'content' => 'Lorem ipsum dolor'
		);

		$dp = DatapointFactory::create($dp);
		$this->assertInstanceOf('Datapoint_Dataset', $dp);

	}

	public function testNumericArrayReturnsLoopableDatapoint()
	{

		$dp = array(
			array(
				'title' => 'Test Title',
				'content' => 'Test Content'
			),
			array(
				'title' => 'Test Title',
				'content' => 'Test Content'
			)
		);

		$dp = DatapointFactory::create($dp);
		$this->assertInstanceOf('Datapoint_Loopable', $dp);

	}



	public function testDatasetDatapointsParseCorrectly()
	{

		$this->plate->setData(
			array(
				'testDatasetDatapoint'=> array(
					'title' => 'Test Title',
					'content' => 'Test Content'
				)
			)
		);

		$this->plate->setTemplate('{testDatasetDatapoint}{title} - {content}{/testDatasetDatapoint}');

		$this->plate->parse();

		$this->assertEquals( $this->plate->getBuffer(), 'Test Title - Test Content'); 


	}

	public function testLoopableDatapointsParseCorrectly()
	{

		$this->plate->setData(
			array(
				'loop' => array(
					array(
						'title' => 'Test Title',
						'content' => 'Test Content'
					),
					array(
						'title' => 'Test Title',
						'content' => 'Test Content'
					),
				)
			)
		);

		$this->plate->setTemplate('{loop}{title} - {content} - {/loop}');

		$this->plate->parse();

		$this->assertEquals( $this->plate->getBuffer(), 'Test Title - Test Content - Test Title - Test Content - ');

	}

	public function testConditionalDirectiveWorks()
	{
		$this->plate->setData(
			array(
				'title' => 'My Title',
				'content' => 'My Content'
			)
		);

		// true conditions succeed
		$this->plate->setTemplate('{if {title} == "My Title"}Title is {title}!{/if}');
		$this->plate->parse();

		$this->assertEquals("Title is My Title!", $this->plate->getBuffer());

		// false conditions fail
		$this->plate->setTemplate('...{if {title} == "My Title!"}Title is {title}!{/if}');
		$this->plate->parse();

		$this->assertEquals("...", $this->plate->getBuffer());

		// inequalities work
		$this->plate->setTemplate('{if {title} !== "My Title!"}Title is {title}!{/if}');
		$this->plate->parse();

		$this->assertEquals("Title is My Title!", $this->plate->getBuffer());

		$this->plate->setTemplate('...{if {title} !== "My Title"}Title is {title}!{/if}');
		$this->plate->parse();

		$this->assertEquals("...", $this->plate->getBuffer());



	}



//	public function testCallingPseudodata

}
