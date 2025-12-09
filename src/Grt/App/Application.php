<?php

namespace Mwb\Grt\App;

/**
 * a GRT application object
 */
class Application extends \Mwb\Grt_Object
{
    /**
     * @var $doc ?\Mwb\Grt\App\Document the document the application is working with
     */
    public ?\Mwb\Grt\App\Document $doc = null;

    /**
     * @var $info ?\Mwb\Grt\App\Info information about the application
     */
    public ?\Mwb\Grt\App\Info $info = null;

    /**
     * @var $options ?\Mwb\Grt\App\Options application options
     */
    public ?\Mwb\Grt\App\Options $options = null;

    /**
     * @var $state ?\ArrayObject application state info, keys in format domain:option
     */
    public ?\ArrayObject $state = null;

    /**
     * @var $registry ?\Mwb\Grt\App\Registry information about the application
     */
    public ?\Mwb\Grt\App\Registry $registry = null;

    /**
     * @var $starters ?\Mwb\Grt\App\Starters Application starters
     */
    public ?\Mwb\Grt\App\Starters $starters = null;

    /**
     * @var $customData ?\ArrayObject a generic dictionary to hold additional
     * information used by e.g. plugins
     */
    public ?\ArrayObject $customData = null;
}

