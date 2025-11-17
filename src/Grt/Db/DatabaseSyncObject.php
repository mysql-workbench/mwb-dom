<?php

namespace Mwb\Grt\Db;

use \ArrayObject;
use \Mwb\Grt\NamedObject;

class DatabaseSyncObject extends \Mwb\Grt_Object
{
    public ?int $alterDirection = null;

    public ?int $changed = null;

    public ?\ArrayObject $children = null;

    public ?\Mwb\Grt\NamedObject $dbObject = null;

    public ?\Mwb\Grt\NamedObject $modelObject = null;

    public ?\ArrayObject $syncLog = null;
}

