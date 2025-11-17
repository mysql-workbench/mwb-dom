<?php

namespace Mwb\Grt\App;

use \ArrayObject;
use \Mwb\Grt\App\Document;
use \Mwb\Grt\App\Info;
use \Mwb\Grt\App\Options;
use \Mwb\Grt\App\Registry;
use \Mwb\Grt\App\Starters;

class Application extends \Mwb\Grt_Object
{
    public ?\ArrayObject $customData = null;

    public ?\Mwb\Grt\App\Document $doc = null;

    public ?\Mwb\Grt\App\Info $info = null;

    public ?\Mwb\Grt\App\Options $options = null;

    public ?\Mwb\Grt\App\Registry $registry = null;

    public ?\Mwb\Grt\App\Starters $starters = null;

    public ?\ArrayObject $state = null;
}

