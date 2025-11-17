<?php

namespace Mwb\Grt\Db\Mysql;

class LogFileGroup extends \Mwb\Grt\Db\LogFileGroup
{
    public ?string $engine = null;

    public ?int $nodeGroupId = null;

    public ?int $wait = null;
}

