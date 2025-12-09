<?php

namespace Mwb\Grt\Db\Query;

/**
 * a query resultset. This object does not allow changes to the resultset, if you
 * need to edit the resultset, see \ref db_query_EditableResultset
 */
class Resultset extends \Mwb\Grt_Object
{
    /**
     * @var $sql ?string the SQL statement that generated this resultset
     */
    public ?string $sql = null;

    /**
     * @var $columns ?\ArrayObject the columns of the resultset
     */
    public ?\ArrayObject $columns = null;

    /**
     * @var $rowCount ?int
     */
    public ?int $rowCount = null;

    /**
     * @var $currentRow ?int
     */
    public ?int $currentRow = null;
}

