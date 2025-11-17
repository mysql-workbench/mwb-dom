<?php

namespace Mwb\Grt\Db;

use \ArrayObject;
use \Mwb\Grt\Db\Index;
use \Mwb\Grt\Db\Table;

class ForeignKey extends \Mwb\Grt\NamedObject
{
    public ?\ArrayObject $columns = null;

    public ?\ArrayObject $customData = null;

    public ?int $deferability = null;

    public ?string $deleteRule = null;

    public ?\Mwb\Grt\Db\Index $index = null;

    public ?int $mandatory = null;

    public ?int $many = null;

    public ?int $modelOnly = null;

    public ?\ArrayObject $referencedColumns = null;

    public ?int $referencedMandatory = null;

    public ?\Mwb\Grt\Db\Table $referencedTable = null;

    public ?string $updateRule = null;
}

