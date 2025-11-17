<?php

namespace Mwb\Grt\Db\Migration;

use \ArrayObject;
use \Mwb\Grt\Db\Catalog;
use \Mwb\Grt\Db\Mgmt\Connection;
use \Mwb\Grt\Version;

class Migration extends \Mwb\Grt_Object
{
    public ?\ArrayObject $applicationData = null;

    public ?\ArrayObject $creationLog = null;

    public ?\ArrayObject $dataBulkTransferParams = null;

    public ?\ArrayObject $dataTransferLog = null;

    public ?\ArrayObject $defaultColumnValueMappings = null;

    public ?\ArrayObject $genericDatatypeMappings = null;

    public ?\ArrayObject $ignoreList = null;

    public ?\ArrayObject $migrationLog = null;

    public ?\ArrayObject $objectCreationParams = null;

    public ?\ArrayObject $objectMigrationParams = null;

    public ?\ArrayObject $selectedSchemataNames = null;

    public ?\Mwb\Grt\Db\Catalog $sourceCatalog = null;

    public ?\Mwb\Grt\Db\Mgmt\Connection $sourceConnection = null;

    public ?\Mwb\Grt\Version $sourceDBVersion = null;

    public ?\ArrayObject $sourceObjects = null;

    public ?\ArrayObject $sourceSchemataNames = null;

    public ?\Mwb\Grt\Db\Catalog $targetCatalog = null;

    public ?\Mwb\Grt\Db\Mgmt\Connection $targetConnection = null;

    public ?\Mwb\Grt\Version $targetDBVersion = null;

    public ?\Mwb\Grt\Version $targetVersion = null;
}

