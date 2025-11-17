<?php

namespace Mwb\Grt\Db;

use \ArrayObject;

class View extends \Mwb\Grt\Db\DatabaseDdlObject
{
    public ?int $algorithm = null;

    public ?\ArrayObject $columns = null;

    public ?int $isReadOnly = null;

    public ?string $oldModelSqlDefinition = null;

    public ?string $oldServerSqlDefinition = null;

    public ?int $withCheckCondition = null;
}

