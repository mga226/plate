# Plate

Flexible and extensible PHP templating and data structure.

Extracted from Contour CMS, and not fully working yet.

## Sample Usage

```php
$data = array(
	'title' => 'My Blog Post',
	'content' => 'Lorem ipsum dolor est...',
	'date' => '2014-06-01',
	'author' => array(
		'name' => 'Mike Acreman',
		'email' => 'hi@mikeacreman.com'
	)
);

// or you can specify the data types for each field

$data = array(
	'title' => new PlateField_Text('My Blog Post'),
	'content' => new PlateField_Text('Lorem ipsum dolor est...'),
	'date' => new PlateField_Date('2014-06-01'),
	'author' => array(
		'name' => new PlateField_String('Mike Acreman'),
		'email' => new PlateField_String('hi@mikeacreman.com')
	)
);


// create a dataset

$dataset = new Dataset($data);

$plate = new Plate();
$plate->setData($dataset);
$plate->setTemplate('blog_template.php');
$plate->parseString('');

$buffer = $plate->getBuffer();

echo $buffer;```
