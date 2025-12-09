<?php

namespace Mwb\Grt\Db;

/**
 * an object used for synchronisation
 */
class DatabaseSync extends \Mwb\Grt_Object
{
    /**
     * @var $dbCatalog ?\Mwb\Grt\Db\Catalog the database's catalog
     */
    public ?\Mwb\Grt\Db\Catalog $dbCatalog = null;

    /**
     * @var $changeTree ?\Mwb\Grt\Db\DatabaseSyncObject the tree of changes to apply
     */
    public ?\Mwb\Grt\Db\DatabaseSyncObject $changeTree = null;
}

