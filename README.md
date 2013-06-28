# SilverStripe DataObject Preview

Provides the ability to preview DataObjects in the CMS.

## Installation (with composer)

	$ composer require heyday/silverstripe-dataobjectpreview

## Example

![DataObject Preview Example](resources/example.png?raw=true)

## Usage

DataObjects that you want to preview must implement, `DataObjectPreviewInterface`, which consists of one method `getWkHtmlInput`.

This method `getWkHtmlInput` must return an instance of `Heyday\SilverStripe\WkHtml\Input\InputInterface`

`getWkHtmlInput`

```php
public function getWkHtmlInput()
{
	return new String(
		'Some dataobject preview'
	);
}
```

### DataObjectPreviewField

`getCMSFields`

```php
$generator = new Knp\Snappy\Image('/bin/wkhtmltoimage');

$generator->setOptions(
	array(
		'width'  => 500,
		'format' => 'png'
	)
);

$fields->addFieldToTab(
	'Root.Main',
	new DataObjectPreviewField(
		'SomeDataObject',
		$this,
		$generator
	)
);
```

### GridFieldDataObjectPreview

`getCMSFields`

```php
$fields->addFieldsToTab(
	'Root.Items',
	new GridField(
		'Items',
		'Items',
		$this->Items(),
		$config = GridFieldConfig_RelationEditor::create()
	)
);

$generator = new Knp\Snappy\Image('/bin/wkhtmltoimage');

$generator->setOptions(
	array(
		'width'  => 500,
		'format' => 'png'
	)
);

$config->addComponent(new GridFieldDataObjectPreview($generator));
```