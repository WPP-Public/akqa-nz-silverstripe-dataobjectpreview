<?php

/**
 * Class GridFieldDataObjectPreview
 */
class GridFieldDataObjectPreview implements GridField_ColumnProvider, GridField_HTMLProvider
{
    /**
     * @var DataObjectPreviewer
     */
    protected $previewer;
    /**
     * @param DataObjectPreviewer $previewer
     */
    public function __construct(DataObjectPreviewer $previewer)
    {
        $this->previewer = $previewer;
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
     * @param  GridField $gridField
     * @return array
     */
    public function getColumnsHandled($gridField)
    {
        return array('Preview');
    }
    /**
     * @param  GridField   $gridField
     * @param  DataObject  $record
     * @param  string      $columnName
     * @return bool|string
     */
    public function getColumnContent($gridField, $record, $columnName)
    {
        if ($record instanceof DataObjectPreviewInterface) {
            return $this->previewer->preview($record);
        } else {
            return false;
        }
    }
    /**
     * @param  GridField  $gridField
     * @param  DataObject $record
     * @param  string     $columnName
     * @return array
     */
    public function getColumnAttributes($gridField, $record, $columnName)
    {
        return array(
            'class' => 'col-' . $columnName . ' gridfield-preview'
        );
    }
    /**
     * @param  GridField $gridField
     * @param  string    $columnName
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
        Requirements::css(DATAOBJECTPREVIEW_DIR . '/css/GridFieldDataObjectPreview.css');
    }
    /**
     * End GridField_HTMLProvider
     */
}
