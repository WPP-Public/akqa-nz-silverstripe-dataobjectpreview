<?php

/**
 * Class DataObjectPreviewInterface
 */
interface DataObjectPreviewInterface
{
    /**
     * @return Heyday\SilverStripe\WkHtml\Input\InputInterface
     */
    public function getPreviewHtml();
}
