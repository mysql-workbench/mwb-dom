<?php

namespace Mwb\Grt\Db;

/**
 * an object that stores information about a database schema table
 */
class Table extends \Mwb\Grt\Db\DatabaseObject
{
    /**
     * @var $isTemporary ?int
     */
    public ?int $isTemporary = null;

    /**
     * @var $temporaryScope ?string
     */
    public ?string $temporaryScope = null;

    /**
     * @var $isSystem ?int
     */
    public ?int $isSystem = null;

    /**
     * @var $primaryKey ?\Mwb\Grt\Db\Index
     */
    public ?\Mwb\Grt\Db\Index $primaryKey = null;

    /**
     * @var $columns ?\ArrayObject
     */
    public ?\ArrayObject $columns = null;

    /**
     * @var $indices ?\ArrayObject
     */
    public ?\ArrayObject $indices = null;

    /**
     * @var $foreignKeys ?\ArrayObject
     */
    public ?\ArrayObject $foreignKeys = null;

    /**
     * @var $triggers ?\ArrayObject
     */
    public ?\ArrayObject $triggers = null;

    /**
     * @var $isStub ?int whether this table is a stub intended for foreign keys and
     * triggers that refer to tables external to this model
     */
    public ?int $isStub = null;
}

