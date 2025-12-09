<?php

namespace Mwb\Grt\Db;

/**
 * a object that stores information about a database schema view
 */
class View extends \Mwb\Grt\Db\DatabaseDdlObject
{
    /**
     * @var $algorithm ?int
     */
    public ?int $algorithm = null;

    /**
     * @var $name string the current name of the object
     */
    public ?string $name = null;

    /**
     * @var $withCheckCondition ?int
     */
    public ?int $withCheckCondition = null;

    /**
     * @var $isReadOnly ?int
     */
    public ?int $isReadOnly = null;

    /**
     * @var $columns ?\ArrayObject
     */
    public ?\ArrayObject $columns = null;

    /**
     * @var $oldModelSqlDefinition ?string this is set at the time of the last
     * sync/rev-eng/fwd-eng to be able to tell if the SQL has been altered when the
     * next sync is performed
     */
    public ?string $oldModelSqlDefinition = null;

    /**
     * @var $oldServerSqlDefinition ?string this is set at the time of the last
     * sync/rev-eng/fwd-eng to be able to tell if the SQL has been altered when the
     * next sync is performed
     */
    public ?string $oldServerSqlDefinition = null;
}

