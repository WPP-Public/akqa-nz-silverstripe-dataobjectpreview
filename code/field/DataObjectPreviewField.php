<?php

/**
 * Class DataObjectPreviewField
 */
class DataObjectPreviewField extends DatalessField
{
    /**
     * @var DataObjectPreviewInterface
     */
    protected $record;
    /**
     * @var Knp\Snappy\AbstractGenerator
     */
    protected $generator;
    /**
     * @param The                                   $name
     * @param DataObjectPreviewInterface            $record
     * @param \Knp\Snappy\AbstractGenerator $generator
     */
    public function __construct(
        $name,
        DataObjectPreviewInterface $record,
        Knp\Snappy\AbstractGenerator $generator
    ) {
        $this->record = $record;
        $this->generator = $generator;
        parent::__construct(
            $name
        );
    }
    /**
     * @param array $properties
     * @return string
     */
    public function Field($properties = array())
    {
        try {
            $content = $this->record->getWkHtmlInput()->process();
            $options = $this->generator->getOptions();
            $filepath = GRIDFIELDPREVIEW_CACHE_PATH.'/'.md5($content).'.'.$options['format'];
            if (!file_exists($filepath)) {
                $output = new \Heyday\SilverStripe\WkHtml\Output\File($filepath);
                $output->process($content, $this->generator);
            }
            return sprintf(
                '<img style="max-width: %spx;width: 100%%" src="%s"/>',
                $options['width'],
                str_replace(BASE_PATH, '', $filepath)
            );
        } catch (Exception $e) {
            return 'Image generation failed';
        }
    }
}