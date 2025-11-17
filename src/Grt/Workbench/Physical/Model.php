<?php

namespace Mwb\Grt\Workbench\Physical;

use \Mwb\Grt\Db\Catalog;
use \ArrayObject;
use \Mwb\Grt\Db\Mgmt\Connection;
use \Mwb\Grt\Db\Mgmt\Rdbms;

class Model extends \Mwb\Grt\Model\Model
{
    public ?\Mwb\Grt\Db\Catalog $catalog = null;

    public ?string $connectionNotation = null;

    public ?\ArrayObject $connections = null;

    public ?\Mwb\Grt\Db\Mgmt\Connection $currentConnection = null;

    public ?string $figureNotation = null;

    public ?\ArrayObject $notes = null;

    public ?\Mwb\Grt\Db\Mgmt\Rdbms $rdbms = null;

    public ?\ArrayObject $scripts = null;

    public ?\ArrayObject $syncProfiles = null;

    public ?\ArrayObject $tagCategories = null;

    public ?\ArrayObject $tags = null;
}

