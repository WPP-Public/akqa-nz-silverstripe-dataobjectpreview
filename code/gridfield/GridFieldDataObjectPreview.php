<?php

/**
 * Class GridFieldDataObjectPreview
 */
class GridFieldDataObjectPreview implements GridField_ColumnProvider, GridField_HTMLProvider
{
    /**
     * @var Heyday\SilverStripe\WkHtml\Generator
     */
    protected $generator;
    /**
     * @param \Heyday\SilverStripe\WkHtml\Generator $generator
     */
    public function __construct(
        Heyday\SilverStripe\WkHtml\Generator $generator
    ) {
        $this->generator = $generator;
    }
    /**
     * @param array $options
     */
    public function setGeneratorOptions(array $options)
    {
        $this->generator->getGenerator()->setOptions($options);
    }
    /**
     * Start GridField_ColumnProvider
     */

    /**
     * @param GridField $gridField
     * @param           $columns
     */
    public function augmentColumns($gridField, &$columns)
    {
        if (!in_array('Preview', $columns)) {
            array_unshift($columns, 'Preview');
        }
    }
    /**
     * @param GridField $gridField
     * @return array
     */
    public function getColumnsHandled($gridField)
    {
        return array('Preview');
    }
    /**
     * @param GridField  $gridField
     * @param DataObject $record
     * @param string     $columnName
     * @return bool|string
     */
    public function getColumnContent($gridField, $record, $columnName)
    {
        if ($record instanceof DataObjectPreviewInterface) {
            $this->generator->setInput($record->getWkHtmlInput());
            $options = $this->generator->getGenerator()->getOptions();
            return sprintf(
                '<img style="max-width: %spx;width: 100%%" src="data:image/%s;base64,%s"/>',
                $options['width'],
                $options['format'],
                base64_encode($this->generator->process())
            );
        } else {
            return false;
        }
    }
    /**
     * @param GridField  $gridField
     * @param DataObject $record
     * @param string     $columnName
     * @return array
     */
    public function getColumnAttributes($gridField, $record, $columnName)
    {
        return array(
            'class' => 'col-' . $columnName . ' gridfield-preview'
        );
    }
    /**
     * @param GridField $gridField
     * @param string    $columnName
     * @return array
     */
    public function getColumnMetadata($gridField, $columnName)
    {
        return array('title' => $columnName);
    }
    /**
     * End GridField_ColumnProvider
     */

    /**
     * Start GridField_HTMLProvider
     */
    public function getHTMLFragments($gridField)
    {
        Requirements::css(GRIDFIELDLPREVIEW_DIR . '/css/GridFieldDataObjectPreview.css');
    }
    /**
     * End GridField_HTMLProvider
     */

    /**
     * @param \Heyday\SilverStripe\WkHtml\Generator $generator
     */
    public function setGenerator($generator)
    {
        $this->generator = $generator;
    }
    /**
     * @return \Heyday\SilverStripe\WkHtml\Generator
     */
    public function getGenerator()
    {
        return $this->generator;
    }
}