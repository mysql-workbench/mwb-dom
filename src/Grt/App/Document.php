<?php

namespace Mwb\Grt\App;

use \ArrayObject;
use \Mwb\Grt\App\DocumentInfo;
use \Mwb\Grt\App\PageSettings;

class Document extends \Mwb\Grt_Object
{
    public ?\ArrayObject $customData = null;

    public ?\Mwb\Grt\App\DocumentInfo $info = null;

    public ?\Mwb\Grt\App\PageSettings $pageSettings = null;
}

