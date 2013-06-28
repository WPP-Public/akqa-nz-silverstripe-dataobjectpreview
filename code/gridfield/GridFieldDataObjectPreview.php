<?php
use Heyday\SilverStripe\WkHtml\Output\File;

/**
 * Class GridFieldDataObjectPreview
 */
class GridFieldDataObjectPreview implements GridField_ColumnProvider, GridField_HTMLProvider
{
    /**
     * @var Knp\Snappy\AbstractGenerator
     */
    protected $generator;
    /**
     * @var Raven_Client
     */
    protected $logger;
    /**
     * @param \Knp\Snappy\AbstractGenerator $generator
     */
    public function __construct(
        Knp\Snappy\AbstractGenerator $generator,
        Raven_Client $logger = null
    ) {
        $this->generator = $generator;
        $this->logger = $logger;
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
            try {
                $content = $record->getWkHtmlInput()->process();
                $options = $this->generator->getOptions();
                $filepath = sprintf(
                    '%s/%s.%s',
                    GRIDFIELDPREVIEW_CACHE_PATH,
                    md5($content),
                    $options['format']
                );
                if (!file_exists($filepath)) {
                    $output = new File($filepath);
                    $output->process($content, $this->generator);
                }

                return sprintf(
                    '<img style="max-width: %spx;width: 100%%" src="%s"/>',
                    $options['width'],
                    str_replace(BASE_PATH, '', $filepath)
                );
            } catch (Exception $e) {
                if (null !== $this->logger) {
                    $this->logger->captureException($e);
                }

                return 'Image generation failed';
            }
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
        Requirements::css(GRIDFIELDPREVIEW_DIR . '/css/GridFieldDataObjectPreview.css');
    }
    /**
     * End GridField_HTMLProvider
     */

    /**
     * @param \Knp\Snappy\AbstractGenerator $generator
     */
    public function setGenerator($generator)
    {
        $this->generator = $generator;
    }
    /**
     * @return \Knp\Snappy\AbstractGenerator
     */
    public function getGenerator()
    {
        return $this->generator;
    }
}