<?php

namespace Mwb\Grt\App;

/**
 * information about the application
 */
class Document extends \Mwb\Grt_Object
{
    /**
     * @var $info ?\Mwb\Grt\App\DocumentInfo user supplied info about the document
     */
    public ?\Mwb\Grt\App\DocumentInfo $info = null;

    /**
     * @var $pageSettings ?\Mwb\Grt\App\PageSettings
     */
    public ?\Mwb\Grt\App\PageSettings $pageSettings = null;

    /**
     * @var $customData ?\ArrayObject
     */
    public ?\ArrayObject $customData = null;
}

