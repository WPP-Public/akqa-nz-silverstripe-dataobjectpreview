<?php

use Heyday\SilverStripe\WkHtml\Output\File;
use Knp\Snappy\AbstractGenerator;
use Symfony\Component\Process\Process;

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
     * @var bool
     */
    protected $backgroundProcessing = false;
    /**
     * @param \Knp\Snappy\AbstractGenerator $generator
     * @param ImageOptimiserInterface       $optimiser
     * @param Raven_Client                  $logger
     * @param null                          $backgroundProcessing
     */
    public function __construct(
        AbstractGenerator $generator,
        ImageOptimiserInterface $optimiser = null,
        Raven_Client $logger = null,
        $backgroundProcessing = null
    ) {
        $this->generator = $generator;
        $this->optimiser = $optimiser;
        $this->logger = $logger;
        if (is_bool($backgroundProcessing)) {
            $this->backgroundProcessing = $backgroundProcessing;
        }
    }
    /**
     * @param boolean $backgroundProcessing
     */
    public function setBackgroundProcessing($backgroundProcessing)
    {
        $this->backgroundProcessing = $backgroundProcessing;
    }
    /**
     * @return boolean
     */
    public function getBackgroundProcessing()
    {
        return $this->backgroundProcessing;
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
            $contentMd5 = md5($content);
            $imageFilepath = sprintf(
                '%s/%s.%s',
                DATAOBJECTPREVIEW_CACHE_PATH,
                $contentMd5,
                $options['format']
            );

            if (!file_exists($imageFilepath)) {
                if ($this->backgroundProcessing) {
                    $htmlFilepath = sprintf(
                        '%s/%s.html',
                        DATAOBJECTPREVIEW_CACHE_PATH,
                        $contentMd5
                    );

                    if (!file_exists($htmlFilepath)) {
                        file_put_contents(
                            $htmlFilepath,
                            $content
                        );
                    }

                    $process = new Process(
                        $cmd = sprintf(
                            'php %s/cli-script.php silverstripe-dataobjectpreview/generate/%s format=%s width=%s > /dev/null 2>&1 &',
                            FRAMEWORK_PATH,
                            $contentMd5,
                            $options['format'],
                            $options['width']
                        )
                    );

                    $process->run();

                } else {
                    $output = new File($imageFilepath);
                    $output->process($content, $this->generator);
                    if (null !== $this->optimiser) {
                        $this->optimiser->optimiseImage($imageFilepath);
                    }
                }
            }

            return sprintf(
                '<img style="max-width: %spx;width: 100%%" src="%s"/>',
                $options['width'],
                str_replace(BASE_PATH, '', $imageFilepath)
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
