<?php

namespace Mwb\Grt\Db;

use \ArrayObject;

class User extends \Mwb\Grt\Db\DatabaseObject
{
    public ?string $password = null;

    public ?\ArrayObject $roles = null;
}

