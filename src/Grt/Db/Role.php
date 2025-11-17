<?php

namespace Mwb\Grt\Db;

use \ArrayObject;

class Role extends \Mwb\Grt\Db\DatabaseObject
{
    public ?\ArrayObject $childRoles = null;

    public ?\Mwb\Grt\Db\Role $parentRole = null;

    public ?\ArrayObject $privileges = null;
}

