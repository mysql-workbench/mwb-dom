<?php

namespace Mwb\Grt\Db\Mysql;

class Table extends \Mwb\Grt\Db\Table
{
    /**
     * @var $primaryKey ?\Mwb\Grt\Db\Mysql\Index
     */
    public ?\Mwb\Grt\Db\Index $primaryKey = null;

    /**
     * @var $columns \Mwb\Grt\Db\Mysql\Column[]
     */
    public ?\ArrayObject $columns = null;

    /**
     * @var $indices \Mwb\Grt\Db\Mysql\Index[]
     */
    public ?\ArrayObject $indices = null;

    /**
     * @var $foreignKeys \Mwb\Grt\Db\Mysql\ForeignKey[]
     */
    public ?\ArrayObject $foreignKeys = null;

    /**
     * @var $triggers \Mwb\Grt\Db\Mysql\Trigger[]
     */
    public ?\ArrayObject $triggers = null;

    /**
     * @var $tableEngine ?string
     */
    public ?string $tableEngine = null;

    /**
     * @var $nextAutoInc ?string
     */
    public ?string $nextAutoInc = null;

    /**
     * @var $password ?string
     */
    public ?string $password = null;

    /**
     * @var $delayKeyWrite ?int
     */
    public ?int $delayKeyWrite = null;

    /**
     * @var $defaultCharacterSetName ?string
     */
    public ?string $defaultCharacterSetName = null;

    /**
     * @var $defaultCollationName ?string
     */
    public ?string $defaultCollationName = null;

    /**
     * @var $mergeUnion ?string
     */
    public ?string $mergeUnion = null;

    /**
     * @var $mergeInsert ?string
     */
    public ?string $mergeInsert = null;

    /**
     * @var $tableDataDir ?string
     */
    public ?string $tableDataDir = null;

    /**
     * @var $tableIndexDir ?string
     */
    public ?string $tableIndexDir = null;

    /**
     * @var $packKeys ?string DEFAULT, 0 or 1
     */
    public ?string $packKeys = null;

    /**
     * @var $raidType ?string
     */
    public ?string $raidType = null;

    /**
     * @var $raidChunks ?string
     */
    public ?string $raidChunks = null;

    /**
     * @var $raidChunkSize ?string
     */
    public ?string $raidChunkSize = null;

    /**
     * @var $checksum ?int
     */
    public ?int $checksum = null;

    /**
     * @var $rowFormat ?string
     */
    public ?string $rowFormat = null;

    /**
     * @var $keyBlockSize ?string
     */
    public ?string $keyBlockSize = null;

    /**
     * @var $avgRowLength ?string
     */
    public ?string $avgRowLength = null;

    /**
     * @var $minRows ?string
     */
    public ?string $minRows = null;

    /**
     * @var $maxRows ?string
     */
    public ?string $maxRows = null;

    /**
     * @var $connection ?\Mwb\Grt\Db\ServerLink if this is a federated table the
     * connection is set to the server link object
     */
    public ?\Mwb\Grt\Db\ServerLink $connection = null;

    /**
     * @var $connectionString ?string if this is a federated table the connection is
     * set to the server link object
     */
    public ?string $connectionString = null;

    /**
     * @var $partitionType ?string
     */
    public ?string $partitionType = null;

    /**
     * @var $partitionExpression ?string a generic expression or a column list
     */
    public ?string $partitionExpression = null;

    /**
     * @var $partitionKeyAlgorithm ?int algorithm used for KEY partition type, can be 1
     * or 2
     */
    public ?int $partitionKeyAlgorithm = null;

    /**
     * @var $partitionCount ?int
     */
    public ?int $partitionCount = null;

    /**
     * @var $subpartitionType ?string
     */
    public ?string $subpartitionType = null;

    /**
     * @var $subpartitionKeyAlgorithm ?int algorithm used for KEY partition type, can
     * be 1 or 2
     */
    public ?int $subpartitionKeyAlgorithm = null;

    /**
     * @var $subpartitionExpression ?string
     */
    public ?string $subpartitionExpression = null;

    /**
     * @var $subpartitionCount ?int
     */
    public ?int $subpartitionCount = null;

    /**
     * @var $partitionDefinitions ?\ArrayObject
     */
    public ?\ArrayObject $partitionDefinitions = null;

    /**
     * @var $statsAutoRecalc ?string DEFAULT, 0 or 1
     */
    public ?string $statsAutoRecalc = null;

    /**
     * @var $statsPersistent ?string DEFAULT, 0 or 1
     */
    public ?string $statsPersistent = null;

    /**
     * @var $statsSamplePages ?int
     */
    public ?int $statsSamplePages = null;

    /**
     * @var $tableSpace ?string
     */
    public ?string $tableSpace = null;
}

