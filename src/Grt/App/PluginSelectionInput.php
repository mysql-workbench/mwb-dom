<?php

namespace Mwb\Grt\App;

use \ArrayObject;

class PluginSelectionInput extends \Mwb\Grt\App\PluginInputDefinition
{
    public ?string $argumentCardinality = null;

    public ?\ArrayObject $objectStructNames = null;
}

