<?php

namespace Mwb\Grt\Db\Mssql;

class Index extends \Mwb\Grt\Db\Index
{
    public ?int $clustered = null;

    public ?string $filterDefinition = null;

    public ?int $hasFilter = null;

    public ?int $ignoreDuplicateRows = null;
}

