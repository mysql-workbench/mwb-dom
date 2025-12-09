<?php

namespace Mwb\Grt\Db\Mgmt;

/**
 * information about a database driver
 */
class Driver extends \Mwb\Grt_Object
{
    /**
     * @var $caption ?string the caption that is displayed in the UI
     */
    public ?string $caption = null;

    /**
     * @var $description ?string a short description of the driver
     */
    public ?string $description = null;

    /**
     * @var $filesTarget ?string location where the driver files are installed
     */
    public ?string $filesTarget = null;

    /**
     * @var $files ?\ArrayObject filename(s) of the driver
     */
    public ?\ArrayObject $files = null;

    /**
     * @var $driverLibraryName ?string location of the driver library
     */
    public ?string $driverLibraryName = null;

    /**
     * @var $parameters ?\ArrayObject the parameters the driver supports
     */
    public ?\ArrayObject $parameters = null;

    /**
     * @var $hostIdentifierTemplate ?string
     */
    public ?string $hostIdentifierTemplate = null;
}

