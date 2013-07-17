<?php

/**
 * Class DataObjectPreviewer
 */
class DataObjectPreviewer
{
    /**
     * @param DataObjectPreviewInterface $record
     * @return string
     */
    public function preview(DataObjectPreviewInterface $record)
    {
        $content = $record->getPreviewHtml();
        $contentMd5 = md5($content);

        $htmlFilepath = sprintf(
            '%s/%s.html',
            DATAOBJECTPREVIEW_CACHE_PATH,
            $contentMd5
        );

        if (!file_exists($htmlFilepath)) {
            file_put_contents($htmlFilepath, $content);
        }

        return sprintf(
            '<div class="dataobjectpreview" data-src="%s"></div>',
            str_replace(BASE_PATH, '', $htmlFilepath)
        );
    }
}
