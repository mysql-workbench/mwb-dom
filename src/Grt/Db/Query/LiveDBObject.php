<?php

namespace Mwb\Grt\Db\Query;

/**
 * object name from a live database
 */
class LiveDBObject extends \Mwb\Grt_Object
{
    /**
     * @var $type ?string type of the object (schema, table, view, routine)
     */
    public ?string $type = null;

    /**
     * @var $schemaName ?string name of the schema the object belongs to
     */
    public ?string $schemaName = null;
}

