<?php

namespace Mwb\Grt\Db\Mysql;

class Index extends \Mwb\Grt\Db\Index
{
    public ?string $algorithm = null;

    public ?string $indexKind = null;

    public ?int $keyBlockSize = null;

    public ?string $lockOption = null;

    public ?string $withParser = null;
}

