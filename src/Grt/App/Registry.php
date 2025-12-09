<?php

namespace Mwb\Grt\App;

/**
 * registry that keeps dynamic information used by the application
 */
class Registry extends \Mwb\Grt_Object
{
    /**
     * @var $pluginGroups ?\ArrayObject the list of available plugin groups
     */
    public ?\ArrayObject $pluginGroups = null;

    /**
     * @var $plugins ?\ArrayObject the list of available plugins
     */
    public ?\ArrayObject $plugins = null;

    /**
     * @var $customDataFields ?\ArrayObject
     */
    public ?\ArrayObject $customDataFields = null;

    /**
     * @var $appDataDirectory ?string
     */
    public ?string $appDataDirectory = null;

    /**
     * @var $appExecutablePath ?string
     */
    public ?string $appExecutablePath = null;
}

