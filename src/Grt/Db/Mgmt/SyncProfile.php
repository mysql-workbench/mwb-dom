<?php

namespace Mwb\Grt\Db\Mgmt;

use \ArrayObject;

class SyncProfile extends \Mwb\Grt_Object
{
    public ?\ArrayObject $lastKnownDBNames = null;

    public ?\ArrayObject $lastKnownViewDefinitions = null;

    public ?string $lastSyncDate = null;

    public ?string $targetHostIdentifier = null;

    public ?string $targetSchemaName = null;
}

