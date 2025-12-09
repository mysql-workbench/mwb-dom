<?php

namespace Mwb\Grt;

/**
 * Version Information
 *
 * version information for an object
 */
class Version extends \Mwb\Grt_Object
{
    /**
     * @var int $majorNumber major version
     */
    public ?int $majorNumber = null;

    /**
     * @var int $minorNumber minor version
     */
    public ?int $minorNumber = null;

    /**
     * @var int $releaseNumber release number
     */
    public ?int $releaseNumber = null;

    /**
     * @var int $buildNumber build number
     */
    public ?int $buildNumber = null;

    /**
     * @var int $status the status, 0 for GA, 1 for alpha, 2 for beta, 3 for RC
     */
    public ?int $status = null;
}

