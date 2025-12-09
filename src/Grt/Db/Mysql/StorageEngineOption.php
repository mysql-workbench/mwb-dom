<?php

namespace Mwb\Grt\Db\Mysql;

/**
 * an option description for a MySQL storage engine
 */
class StorageEngineOption extends \Mwb\Grt\NamedObject
{
    /**
     * @var $caption ?string
     */
    public ?string $caption = null;

    /**
     * @var $description ?string
     */
    public ?string $description = null;

    /**
     * @var $type ?string
     */
    public ?string $type = null;
}

