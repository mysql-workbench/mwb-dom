<?php

namespace Mwb\Grt\Db\Mysql;

use \ArrayObject;

class PartitionDefinition extends \Mwb\Grt_Object
{
    public ?string $comment = null;

    public ?string $dataDirectory = null;

    public ?string $engine = null;

    public ?string $indexDirectory = null;

    public ?string $maxRows = null;

    public ?string $minRows = null;

    public ?int $nodeGroupId = null;

    public ?\ArrayObject $subpartitionDefinitions = null;

    public ?string $tableSpace = null;

    public ?string $value = null;
}

