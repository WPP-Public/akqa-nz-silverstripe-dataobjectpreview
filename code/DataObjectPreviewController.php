<?php

use Heyday\SilverStripe\WkHtml\Output\File;

/**
 * Class DataObjectPreviewController
 */
class DataObjectPreviewController extends Controller
{
    /**
     * @var array
     */
    private static $dependencies = array(
        'generator' => '%$ImageGenerator',
        'optimiser' => '%$OptimisedGDBackend',
        'logger'    => '%$Raven'
    );
    /**
     * @var Knp\Snappy\AbstractGenerator
     */
    public $generator;
    /**
     * @var ImageOptimiserInterface
     */
    public $optimiser;
    /**
     * @var Raven_Client
     */
    public $logger;
    /**
     * @var array
     */
    private static $allowed_actions = array(
        'cache',
        'generate'
    );
    /**
     * @param SS_HTTPRequest $request
     * @return SS_HTTPResponse
     */
    public function cache(SS_HTTPRequest $request)
    {
        /** @var Config_ForClass $config */
        $config = $this->config();
        $timeoutInterval = (int) $config->get('timeout') * 1000000;
        $loopInterval = (int) $config->get('loopInterval');
        $filename = DATAOBJECTPREVIEW_CACHE_PATH . '/' . $request->param('ID') . '.' . $request->getExtension();

        while ($timeoutInterval > 0) {
            if (!file_exists($filename)) {
                usleep($loopInterval);
                $timeoutInterval -= $loopInterval;
            } elseif (strpos(realpath($filename), realpath(DATAOBJECTPREVIEW_CACHE_PATH)) !== 0) {
                $this->response->setStatusCode(400);
                $this->response->setBody('Bad Request');
                return $this->response;
            } else {
                if ($config->get('xsendfile')) {
                    $this->response->addHeader('X-SendFile', realpath($filename));
                    return $this->response;
                } else {
                    return SS_HTTPRequest::send_file(file_get_contents($filename), basename($filename));
                }
            }
        }

        $this->response->setStatusCode(404);
        $this->response->setBody('Not found');

        return $this->response;
    }
    /**
     * @param SS_HTTPRequest $request
     */
    public function generate(SS_HTTPRequest $request)
    {
        $md5 = $request->param('ID');
        $contentFilename = DATAOBJECTPREVIEW_CACHE_PATH . '/' . $md5 . '.html';
        $tmpImageFilename = DATAOBJECTPREVIEW_CACHE_PATH . '/' . $md5 . '.tmp';

        if (
            !Director::is_cli()
            || empty($md5)
            || !file_exists($contentFilename)
            || null === $this->generator
        ) {
            exit(1);
        }

        $options = $this->generator->getOptions();

        foreach ($request->getVars() as $name => $value) {
            if (array_key_exists($name, $options)) {
                $options[$name] = $value;
            }
        }

        $this->generator->setOptions($options);

        try {
            $this->generator->generate(
                $contentFilename,
                $tmpImageFilename
            );

            if (null !== $this->optimiser) {
                $this->optimiser->optimiseImage($tmpImageFilename);
            }
        } catch (Exception $e) {
            if (null !== $this->logger) {
                $this->logger->captureException(
                    $e,
                    array(
                        'extra' => array(
                            'filename' => $contentFilename
                        )
                    )
                );
            }

            echo 'Image generation failed', PHP_EOL;
            exit(1);
        }

        rename(
            $tmpImageFilename,
            DATAOBJECTPREVIEW_CACHE_PATH . '/' . $md5 . '.' . $options['format']
        );

        unlink($contentFilename);

        echo 'Success', PHP_EOL;
        exit;
    }
}