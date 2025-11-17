<?php

namespace Mwb\Grt\App;

use \ArrayObject;

class Registry extends \Mwb\Grt_Object
{
    public ?string $appDataDirectory = null;

    public ?string $appExecutablePath = null;

    public ?\ArrayObject $customDataFields = null;

    public ?\ArrayObject $pluginGroups = null;

    public ?\ArrayObject $plugins = null;
}

