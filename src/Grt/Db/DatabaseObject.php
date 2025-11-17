<?php

namespace Mwb\Grt\Db;

use \ArrayObject;

class DatabaseObject extends \Mwb\Grt\NamedObject
{
    public ?int $commentedOut = null;

    public ?string $createDate = null;

    public ?\ArrayObject $customData = null;

    public ?string $lastChangeDate = null;

    public ?int $modelOnly = null;

    public ?string $temp_sql = null;
}

