<?php
use Heyday\SilverStripe\WkHtml\Output\File;

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
     * @var Raven_Client
     */
    protected $logger;
    /**
     * @param The                           $name
     * @param DataObjectPreviewInterface    $record
     * @param \Knp\Snappy\AbstractGenerator $generator
     * @param Raven_Client                  $logger
     */
    public function __construct(
        $name,
        DataObjectPreviewInterface $record,
        Knp\Snappy\AbstractGenerator $generator,
        Raven_Client $logger = null
    ) {
        $this->record = $record;
        $this->generator = $generator;
        $this->logger = $logger;
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
    }
}