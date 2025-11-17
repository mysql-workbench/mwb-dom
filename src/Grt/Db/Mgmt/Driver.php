<?php

namespace Mwb\Grt\Db\Mgmt;

use \ArrayObject;

class Driver extends \Mwb\Grt_Object
{
    public ?string $caption = null;

    public ?string $description = null;

    public ?string $driverLibraryName = null;

    public ?\ArrayObject $files = null;

    public ?string $filesTarget = null;

    public ?string $hostIdentifierTemplate = null;

    public ?\ArrayObject $parameters = null;
}

