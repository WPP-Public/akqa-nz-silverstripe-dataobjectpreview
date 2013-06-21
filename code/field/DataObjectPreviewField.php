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
     * @var Heyday\SilverStripe\WkHtml\Generator
     */
    protected $generator;
    /**
     * @param The                                   $name
     * @param DataObjectPreviewInterface            $record
     * @param \Heyday\SilverStripe\WkHtml\Generator $generator
     */
    public function __construct(
        $name,
        DataObjectPreviewInterface $record,
        Heyday\SilverStripe\WkHtml\Generator $generator
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
        $this->generator->setInput(
            $this->record->getWkHtmlInput()
        );
        $options = $this->generator->getGenerator()->getOptions();
        try {
            return sprintf(
                '<img style="max-width: %spx;width: 100%%" src="data:image/%s;base64,%s"/>',
                $options['width'],
                $options['format'],
                base64_encode($this->generator->process())
            );
        } catch (Exception $e) {
            return 'Image generation failed';
        }
    }
}