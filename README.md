# Plate: Object-Oriented Template Engine for PHP

Flexible and extensible PHP templating and data structure.

Plate allows 

## Sample Usage

### Instantiating the Parser and setting data

For clarity, let's start with the most verbose method.

```
use \Plate\Dataset;
use \Plate\Datapoint;
use \Plate\Parser;

$data = new Dataset(
	array(
		'title' => new Datapoint\String('My Blog Post'),
		'content' => new Datapoint\String('Lorem ipsum dolor est...'),
		'date' => new Datapoint\Date('2014-06-01'),
		'author' => new Datapoint\Dataset(
			array(
				'name' => new Datapoint\String('Mike Acreman'),
				'email' => new Datapoint\String('hi@mikeacreman.com')
			)
		)
	) 
);

$parser = new Parser();

$parser->setData($data);```

You can avoid directly instantiating subclasses of the Datapoint class by just providing values, as below. The Dataset class will try to guess the most appropriate Datapoint type and will create the Datapoint for you.

```
use \Plate\Dataset;
use \Plate\Parser;

$data = new Dataset(
	array(
		'title' => 'My Blog Post',
		'content' => 'Lorem ipsum dolor est...',
		'date' => '2014-06-01',
		'author' => array(
			'name' => 'Mike Acreman',
			'email' => 'hi@mikeacreman.com'
		)
	) 
);

$parser = new \Plate\Parser();

$parser-> setData($data);
```

...or you can just pretend Datasets and Datapoints don't exist, handing an array right into the Parser:

```
use \Plate\Parser;

$parser = new Parser();
$parser->setData(
	array(
		'title' => 'My Blog Post',
		'content' => 'Lorem ipsum dolor est...',
		'date' => '2014-06-01',
		'author' => array(
			'name' => 'Mike Acreman',
			'email' => 'hi@mikeacreman.com'
		)
	) 
);
```




First, instantiate your parser

```use Plate;

$parser = new Parser();```

###Setting Data

You can set data in a number of ways.

####1. Create a Dataset object

You can create a Dataset like this:



// or you can specify the data types for each field

$data = array(
	'title' => new Datapoint\String('My Blog Post'),
	'content' => new Datapoint\String('Lorem ipsum dolor est...'),
	'date' => new Datapoint\Date('2014-06-01'),
	'author' => array(
		'name' => new Datapoint\String('Mike Acreman'),
		'email' => new Datapoint\String('hi@mikeacreman.com')
	)
);


// create a dataset

$dataset = new Dataset($data);

$plate = new Parser();
$plate->setData($dataset);
$plate->setTemplate('<h1>{title}</h1>');
$plate->parse();

$buffer = $plate->getBuffer();

echo $buffer;
```
