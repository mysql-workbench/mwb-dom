<?php

namespace Mwb\Grt\Db\Mysql;

class PartitionDefinition extends \Mwb\Grt_Object
{
    /**
     * @var $value ?string
     */
    public ?string $value = null;

    /**
     * @var $comment ?string
     */
    public ?string $comment = null;

    /**
     * @var $dataDirectory ?string
     */
    public ?string $dataDirectory = null;

    /**
     * @var $indexDirectory ?string
     */
    public ?string $indexDirectory = null;

    /**
     * @var $maxRows ?string
     */
    public ?string $maxRows = null;

    /**
     * @var $minRows ?string
     */
    public ?string $minRows = null;

    /**
     * @var $tableSpace ?string
     */
    public ?string $tableSpace = null;

    /**
     * @var $engine ?string
     */
    public ?string $engine = null;

    /**
     * @var $nodeGroupId ?int
     */
    public ?int $nodeGroupId = null;

    /**
     * @var $subpartitionDefinitions ?\ArrayObject
     */
    public ?\ArrayObject $subpartitionDefinitions = null;
}

