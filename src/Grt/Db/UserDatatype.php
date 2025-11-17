<?php

namespace Mwb\Grt\Db;

use \Mwb\Grt\Db\SimpleDatatype;

class UserDatatype extends \Mwb\Grt_Object
{
    public ?\Mwb\Grt\Db\SimpleDatatype $actualType = null;

    public ?string $flags = null;

    public ?string $sqlDefinition = null;
}

