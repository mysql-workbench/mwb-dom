<?php

namespace Mwb\Grt\Db;

use \Mwb\Grt\Db\LogFileGroup;

class Tablespace extends \Mwb\Grt\Db\DatabaseObject
{
    public ?int $autoExtendSize = null;

    public ?string $dataFile = null;

    public ?int $extentSize = null;

    public ?int $initialSize = null;

    public ?\Mwb\Grt\Db\LogFileGroup $logFileGroup = null;

    public ?int $maxSize = null;
}

