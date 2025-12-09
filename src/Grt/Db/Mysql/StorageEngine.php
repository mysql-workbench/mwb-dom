<?php

namespace Mwb\Grt\Db\Mysql;

/**
 * a MySQL storage engine type description
 */
class StorageEngine extends \Mwb\Grt\NamedObject
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
     * @var $supportsForeignKeys ?int
     */
    public ?int $supportsForeignKeys = null;

    /**
     * @var $options ?\ArrayObject
     */
    public ?\ArrayObject $options = null;
}

