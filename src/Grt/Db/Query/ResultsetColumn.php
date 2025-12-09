<?php

namespace Mwb\Grt\Db\Query;

/**
 * a database resultset column
 */
class ResultsetColumn extends \Mwb\Grt_Object
{
    /**
     * @var $columnType ?string the type of the column, string, int, real, blob, date,
     * time, datetime, geo
     */
    public ?string $columnType = null;
}

