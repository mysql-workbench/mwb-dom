<?php

namespace Mwb\Grt\Db;

use \ArrayObject;
use \Mwb\Grt\Db\Index;

class Table extends \Mwb\Grt\Db\DatabaseObject
{
    public ?\ArrayObject $columns = null;

    public ?\ArrayObject $foreignKeys = null;

    public ?\ArrayObject $indices = null;

    public ?int $isStub = null;

    public ?int $isSystem = null;

    public ?int $isTemporary = null;

    public ?\Mwb\Grt\Db\Index $primaryKey = null;

    public ?string $temporaryScope = null;

    public ?\ArrayObject $triggers = null;
}

