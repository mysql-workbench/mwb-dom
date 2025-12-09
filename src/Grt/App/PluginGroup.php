<?php

namespace Mwb\Grt\App;

/**
 * groups a number of plugins together
 */
class PluginGroup extends \Mwb\Grt_Object
{
    /**
     * @var $category ?string the category this group belongs to
     */
    public ?string $category = null;

    /**
     * @var $plugins ?\ArrayObject the list of plugins in this group
     */
    public ?\ArrayObject $plugins = null;
}

