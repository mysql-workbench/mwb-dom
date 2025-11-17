<?php

namespace Mwb\Grt\Db;

use \Mwb\Grt\Db\DatabaseObject;
use \ArrayObject;

class RolePrivilege extends \Mwb\Grt_Object
{
    public ?\Mwb\Grt\Db\DatabaseObject $databaseObject = null;

    public ?string $databaseObjectName = null;

    public ?string $databaseObjectType = null;

    public ?\ArrayObject $privileges = null;
}

