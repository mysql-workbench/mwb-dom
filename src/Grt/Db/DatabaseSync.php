<?php

namespace Mwb\Grt\Db;

use \Mwb\Grt\Db\DatabaseSyncObject;
use \Mwb\Grt\Db\Catalog;

class DatabaseSync extends \Mwb\Grt_Object
{
    public ?\Mwb\Grt\Db\DatabaseSyncObject $changeTree = null;

    public ?\Mwb\Grt\Db\Catalog $dbCatalog = null;
}

