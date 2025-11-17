<?php

namespace Mwb\Grt\Db;

use \Mwb\Grt\Db\Column;

class IndexColumn extends \Mwb\Grt_Object
{
    public ?int $columnLength = null;

    public ?string $comment = null;

    public ?int $descend = null;

    public ?\Mwb\Grt\Db\Column $referencedColumn = null;
}

