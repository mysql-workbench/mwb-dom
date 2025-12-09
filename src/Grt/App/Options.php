<?php

namespace Mwb\Grt\App;

/**
 * stores the application's options
 */
class Options extends \Mwb\Grt_Object
{
    /**
     * @var $commonOptions ?\ArrayObject stores options that are shared between
     * applications
     */
    public ?\ArrayObject $commonOptions = null;

    /**
     * @var $options ?\ArrayObject stores application specific options
     */
    public ?\ArrayObject $options = null;

    /**
     * @var $paperTypes ?\ArrayObject
     */
    public ?\ArrayObject $paperTypes = null;

    /**
     * @var $recentFiles ?\ArrayObject recently opened files
     */
    public ?\ArrayObject $recentFiles = null;

    /**
     * @var $disabledPlugins ?\ArrayObject list of plugin names that are disabled
     */
    public ?\ArrayObject $disabledPlugins = null;
}

