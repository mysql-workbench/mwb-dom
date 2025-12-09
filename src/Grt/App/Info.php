<?php

namespace Mwb\Grt\App;

/**
 * information about the application
 */
class Info extends \Mwb\Grt_Object
{
    /**
     * @var $caption ?string the application's caption
     */
    public ?string $caption = null;

    /**
     * @var $version ?\Mwb\Grt\Version the version of the application
     */
    public ?\Mwb\Grt\Version $version = null;

    /**
     * @var $description ?string a short description of the application
     */
    public ?string $description = null;

    /**
     * @var $copyright ?string the copyright message
     */
    public ?string $copyright = null;

    /**
     * @var $license ?string the license message
     */
    public ?string $license = null;

    /**
     * @var $edition ?string the edition name
     */
    public ?string $edition = null;
}

