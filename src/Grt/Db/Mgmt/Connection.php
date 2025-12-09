<?php

namespace Mwb\Grt\Db\Mgmt;

/**
 * a stored RDBMS connection
 */
class Connection extends \Mwb\Grt_Object
{
    /**
     * @var $driver ?\Mwb\Grt\Db\Mgmt\Driver the driver used to connect
     */
    public ?\Mwb\Grt\Db\Mgmt\Driver $driver = null;

    /**
     * @var $parameterValues ?\ArrayObject the parameters the user entered
     */
    public ?\ArrayObject $parameterValues = null;

    /**
     * @var $modules ?\ArrayObject the modules used for this connection
     */
    public ?\ArrayObject $modules = null;

    /**
     * @var $hostIdentifier ?string identifier to be used for storing password
     */
    public ?string $hostIdentifier = null;

    /**
     * @var $isDefault ?int
     */
    public ?int $isDefault = null;
}

