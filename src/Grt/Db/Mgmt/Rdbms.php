<?php

namespace Mwb\Grt\Db\Mgmt;

use \ArrayObject;
use \Mwb\Grt\Db\Mgmt\Driver;
use \Mwb\Grt\Version;

class Rdbms extends \Mwb\Grt_Object
{
    public ?string $caption = null;

    public ?\ArrayObject $characterSets = null;

    public ?string $databaseObjectPackage = null;

    public ?\Mwb\Grt\Db\Mgmt\Driver $defaultDriver = null;

    public ?int $doesSupportCatalogs = null;

    public ?\ArrayObject $drivers = null;

    public ?int $maximumIdentifierLength = null;

    public ?\ArrayObject $privilegeNames = null;

    public ?\ArrayObject $simpleDatatypes = null;

    public ?\Mwb\Grt\Version $version = null;
}

