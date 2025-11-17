<?php

namespace Mwb\Grt\Workbench;

use \Mwb\Grt\Db\Migration\Migration;
use \Mwb\Grt\Db\Mgmt\Management;
use \ArrayObject;

class Workbench extends \Mwb\Grt\App\Application
{
    public ?string $docPath = null;

    public ?\Mwb\Grt\Db\Migration\Migration $migration = null;

    public ?\Mwb\Grt\Db\Mgmt\Management $rdbmsMgmt = null;

    public ?\ArrayObject $sqlEditors = null;
}

