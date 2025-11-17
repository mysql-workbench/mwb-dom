<?php

namespace Mwb\Grt\Db\Mgmt;

use \Mwb\Grt\Db\Mgmt\Driver;
use \ArrayObject;

class Connection extends \Mwb\Grt_Object
{
    public ?\Mwb\Grt\Db\Mgmt\Driver $driver = null;

    public ?string $hostIdentifier = null;

    public ?int $isDefault = null;

    public ?\ArrayObject $modules = null;

    public ?\ArrayObject $parameterValues = null;
}

