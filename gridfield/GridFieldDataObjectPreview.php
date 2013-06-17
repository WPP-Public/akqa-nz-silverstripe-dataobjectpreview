<?php

/**
 * Class GridFieldDataObjectPreview
 */
class GridFieldDataObjectPreview implements GridField_ColumnProvider
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
     */
    public function getColumnsHandled($gridField)
    {
        return array('Preview');
    }
    /**
     * @param GridField  $gridField
     * @param DataObject $record
     * @param string     $columnName
     */
    public function getColumnContent($gridField, $record, $columnName)
    {
        if ($record instanceof GridFieldDataObjectPreviewInterface) {
            $this->generator->setInput($record->getWkHtmlInput());
            return '<img width="500px" src="data:image/jpg;base64,' . base64_encode($this->generator->process()) . '"/>';
        } else {
            return false;
        }
    }
    /**
     * @param GridField  $gridField
     * @param DataObject $record
     * @param string     $columnName
     */
    public function getColumnAttributes($gridField, $record, $columnName)
    {
        return array();
    }
    /**
     * @param GridField $gridField
     * @param string    $columnName
     */
    public function getColumnMetadata($gridField, $columnName)
    {
        return array('title' => $columnName);
    }
}