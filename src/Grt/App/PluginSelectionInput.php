<?php

namespace Mwb\Grt\App;

/**
 * input is a list of objects taken from the source given in name (eg
 * activeDiagram)
 */
class PluginSelectionInput extends \Mwb\Grt\App\PluginInputDefinition
{
    /**
     * @var $objectStructNames ?\ArrayObject the types of objects that can be handled
     * by this plugin
     */
    public ?\ArrayObject $objectStructNames = null;

    /**
     * @var $argumentCardinality ?string defines the number of objects the plugin
     * requires: 1 for exactly 1, ? for 0 or 1, + for 1 or more and * for 0 or more
     */
    public ?string $argumentCardinality = null;
}

