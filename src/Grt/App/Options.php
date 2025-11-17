<?php

namespace Mwb\Grt\App;

use \ArrayObject;

class Options extends \Mwb\Grt_Object
{
    public ?\ArrayObject $commonOptions = null;

    public ?\ArrayObject $disabledPlugins = null;

    public ?\ArrayObject $options = null;

    public ?\ArrayObject $paperTypes = null;

    public ?\ArrayObject $recentFiles = null;
}

