<?php

namespace Mwb\Grt\Db;

class DatabaseObject extends \Mwb\Grt\NamedObject
{
    /**
     * @var $owner ?\Mwb\Grt\NamedObject
     */
    public ?\Mwb\Grt_Object $owner = null;

    /**
     * @var $name ?string
     */
    public ?string $name = null;

    /**
     * @var $temp_sql ?string the generated SQL statement(s)
     */
    public ?string $temp_sql = null;

    /**
     * @var $commentedOut ?int if set to 1 the sql will be commented out but e.g. still
     * be written to the script
     */
    public ?int $commentedOut = null;

    /**
     * @var $customData ?\ArrayObject
     */
    public ?\ArrayObject $customData = null;

    /**
     * @var $createDate ?string
     */
    public ?string $createDate = null;

    /**
     * @var $lastChangeDate ?string
     */
    public ?string $lastChangeDate = null;

    /**
     * @var $modelOnly ?int object exists in model but is not to be written to the
     * database
     */
    public ?int $modelOnly = null;
}

