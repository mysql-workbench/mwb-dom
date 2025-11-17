<?php

namespace Mwb\Grt\Workbench\Physical;

use \Mwb\Grt\Db\Table;

class TableFigure extends \Mwb\Grt\Model\Figure
{
    public ?int $columnsExpanded = null;

    public ?int $foreignKeysExpanded = null;

    public ?int $indicesExpanded = null;

    public ?int $summarizeDisplay = null;

    public ?\Mwb\Grt\Db\Table $table = null;

    public ?int $triggersExpanded = null;
}

