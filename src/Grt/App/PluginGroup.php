<?php

namespace Mwb\Grt\App;

use \ArrayObject;

class PluginGroup extends \Mwb\Grt_Object
{
    public ?string $category = null;

    public ?\ArrayObject $plugins = null;
}

