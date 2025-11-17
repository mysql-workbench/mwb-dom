<?php

namespace Mwb\Grt\Db;

class DatabaseDdlObject extends \Mwb\Grt\Db\DatabaseObject
{
    public ?string $definer = null;

    public ?string $sqlBody = null;

    public ?string $sqlDefinition = null;
}

