# SilverStripe DataObject Preview

Provides the ability to preview DataObjects in the CMS.

## Installation (with composer)

	$ composer require heyday/silverstripe-dataobjectpreview

## Example

![DataObject Preview Example](resources/example.png?raw=true)

## Usage

DataObjects that you want to preview must implement, `DataObjectPreviewInterface`, which consists of one method `getPreviewHtml`.

This method `getPreviewHtml` must return a string.

`getPreviewHtml`

```php
public function getPreviewHtml()
{
	return "<html><body>Hello</body></html>";
}
```

### DataObjectPreviewField

`getCMSFields`

```php

$fields->addFieldToTab(
	'Root.Main',
	new DataObjectPreviewField(
		'SomeDataObject',
		$this,
		new DataObjectPreviewer($generator)
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

$config->addComponent(
	new GridFieldDataObjectPreview(
		new DataObjectPreviewer($generator)
	)
);
```

##License

SilverStripe DataObject Preview is licensed under an [MIT license](http://heyday.mit-license.org/)
