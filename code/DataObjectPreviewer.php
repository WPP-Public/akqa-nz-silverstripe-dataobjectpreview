<?php

use Heyday\SilverStripe\WkHtml\Output\File;
use Knp\Snappy\AbstractGenerator;

/**
 * Class DataObjectPreviewer
 */
class DataObjectPreviewer
{
    /**
     * @var Knp\Snappy\AbstractGenerator
     */
    protected $generator;
    /**
     * @var ImageOptimiserInterface
     */
    protected $optimiser;
    /**
     * @var Raven_Client
     */
    protected $logger;
    /**
     * @param \Knp\Snappy\AbstractGenerator $generator
     * @param ImageOptimiserInterface       $optimiser
     * @param Raven_Client                  $logger
     */
    public function __construct(
        AbstractGenerator $generator,
        ImageOptimiserInterface $optimiser = null,
        Raven_Client $logger = null
    ) {
        $this->generator = $generator;
        $this->optimiser = $optimiser;
        $this->logger = $logger;
    }
    /**
     *
     */
    public function preview(DataObjectPreviewInterface $record)
    {
        $content = '';
        try {
            $content = $record->getWkHtmlInput()->process();
            $options = $this->generator->getOptions();
            $filepath = sprintf(
                '%s/%s.%s',
                DATAOBJECTPREVIEW_CACHE_PATH,
                md5($content),
                $options['format']
            );

            if (!file_exists($filepath)) {
                $output = new File($filepath);
                $output->process($content, $this->generator);
                if (null !== $this->optimiser) {
                    $this->optimiser->optimiseImage($filepath);
                }
            }

            return sprintf(
                '<img style="max-width: %spx;width: 100%%" src="%s"/>',
                $options['width'],
                str_replace(BASE_PATH, '', $filepath)
            );
        } catch (Exception $e) {
            if (null !== $this->logger) {
                $this->logger->captureException(
                    $e,
                    array(
                        'extra' => array(
                            'html' => (string) $content
                        )
                    )
                );
            }

            return 'Image generation failed';
        }
    }
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
    /**
     * @param \Raven_Client $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }
    /**
     * @return \Raven_Client
     */
    public function getLogger()
    {
        return $this->logger;
    }
    /**
     * @param \ImageOptimiserInterface $optimiser
     */
    public function setOptimiser($optimiser)
    {
        $this->optimiser = $optimiser;
    }
    /**
     * @return \ImageOptimiserInterface
     */
    public function getOptimiser()
    {
        return $this->optimiser;
    }
}
