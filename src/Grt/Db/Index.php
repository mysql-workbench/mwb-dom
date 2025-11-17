<?php

namespace Mwb\Grt\Db;

use \ArrayObject;

class Index extends \Mwb\Grt\Db\DatabaseObject
{
    public ?\ArrayObject $columns = null;

    public ?int $deferability = null;

    public ?string $indexType = null;

    public ?int $isPrimary = null;

    public ?int $unique = null;
}

