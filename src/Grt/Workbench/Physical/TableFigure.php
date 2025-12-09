<?php

namespace Mwb\Grt\Workbench\Physical;

/**
 * a model figure representing a table
 */
class TableFigure extends \Mwb\Grt\Model\Figure
{
    /**
     * @var $table ?\Mwb\Grt\Db\Table the table this figure represents
     */
    public ?\Mwb\Grt\Db\Table $table = null;

    /**
     * @var $columnsExpanded ?int indicates whether the columns list is expanded
     */
    public ?int $columnsExpanded = null;

    /**
     * @var $indicesExpanded ?int indicates whether the indices list is expanded
     */
    public ?int $indicesExpanded = null;

    /**
     * @var $foreignKeysExpanded ?int indicates whether the foreign keys list is
     * expanded
     */
    public ?int $foreignKeysExpanded = null;

    /**
     * @var $triggersExpanded ?int indicates whether the triggers list is expanded
     */
    public ?int $triggersExpanded = null;

    /**
     * @var $summarizeDisplay ?int set to -1 for showing table in summarized view mode
     * if there's too many columns, 0 to show all columns and 1 to force summary view
     */
    public ?int $summarizeDisplay = null;
}

