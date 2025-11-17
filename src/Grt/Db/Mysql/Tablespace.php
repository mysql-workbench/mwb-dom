<?php

namespace Mwb\Grt\Db\Mysql;

class Tablespace extends \Mwb\Grt\Db\Tablespace
{
    public ?string $engine = null;

    public ?int $nodeGroupId = null;

    public ?int $wait = null;
}

