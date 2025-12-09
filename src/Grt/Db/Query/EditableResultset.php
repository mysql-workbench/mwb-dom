<?php

namespace Mwb\Grt\Db\Query;

/**
 * a resultset created for editing table data. Changes made to the resultset are
 * queued to be applied when \ref applyChanges() is called
 */
class EditableResultset extends \Mwb\Grt\Db\Query\Resultset
{
    /**
     * @var $schema ?string schema name of the table
     */
    public ?string $schema = null;

    /**
     * @var $table ?string name of the table being edited
     */
    public ?string $table = null;
}

