<?php

namespace Mwb\Grt\Db\Mysql;

use \Mwb\Grt\Db\ServerLink;
use \ArrayObject;

class Table extends \Mwb\Grt\Db\Table
{
    public ?string $avgRowLength = null;

    public ?int $checksum = null;

    public ?\Mwb\Grt\Db\ServerLink $connection = null;

    public ?string $connectionString = null;

    public ?string $defaultCharacterSetName = null;

    public ?string $defaultCollationName = null;

    public ?int $delayKeyWrite = null;

    public ?string $keyBlockSize = null;

    public ?string $maxRows = null;

    public ?string $mergeInsert = null;

    public ?string $mergeUnion = null;

    public ?string $minRows = null;

    public ?string $nextAutoInc = null;

    public ?string $packKeys = null;

    public ?int $partitionCount = null;

    public ?\ArrayObject $partitionDefinitions = null;

    public ?string $partitionExpression = null;

    public ?int $partitionKeyAlgorithm = null;

    public ?string $partitionType = null;

    public ?string $password = null;

    public ?string $raidChunkSize = null;

    public ?string $raidChunks = null;

    public ?string $raidType = null;

    public ?string $rowFormat = null;

    public ?string $statsAutoRecalc = null;

    public ?string $statsPersistent = null;

    public ?int $statsSamplePages = null;

    public ?int $subpartitionCount = null;

    public ?string $subpartitionExpression = null;

    public ?int $subpartitionKeyAlgorithm = null;

    public ?string $subpartitionType = null;

    public ?string $tableDataDir = null;

    public ?string $tableEngine = null;

    public ?string $tableIndexDir = null;

    public ?string $tableSpace = null;
}

