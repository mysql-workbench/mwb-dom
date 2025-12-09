<?php

namespace Mwb\Grt\Db\Migration;

/**
 * an object to store information needed during the migration process
 */
class Migration extends \Mwb\Grt_Object
{
    /**
     * @var $sourceConnection ?\Mwb\Grt\Db\Mgmt\Connection connection used for the
     * source database
     */
    public ?\Mwb\Grt\Db\Mgmt\Connection $sourceConnection = null;

    /**
     * @var $targetConnection ?\Mwb\Grt\Db\Mgmt\Connection connection used for the
     * target database
     */
    public ?\Mwb\Grt\Db\Mgmt\Connection $targetConnection = null;

    /**
     * @var $sourceDBVersion ?\Mwb\Grt\Version
     */
    public ?\Mwb\Grt\Version $sourceDBVersion = null;

    /**
     * @var $targetDBVersion ?\Mwb\Grt\Version
     */
    public ?\Mwb\Grt\Version $targetDBVersion = null;

    /**
     * @var $sourceSchemataNames ?\ArrayObject list of available schemata names in the
     * source database
     */
    public ?\ArrayObject $sourceSchemataNames = null;

    /**
     * @var $selectedSchemataNames ?\ArrayObject list of selected schemata names to
     * reverse engineer
     */
    public ?\ArrayObject $selectedSchemataNames = null;

    /**
     * @var $sourceCatalog ?\Mwb\Grt\Db\Catalog a catalog object reflecting the reverse
     * engineered assets from the source database
     */
    public ?\Mwb\Grt\Db\Catalog $sourceCatalog = null;

    /**
     * @var $targetCatalog ?\Mwb\Grt\Db\Catalog the migrated target catalog
     */
    public ?\Mwb\Grt\Db\Catalog $targetCatalog = null;

    /**
     * @var $targetVersion ?\Mwb\Grt\Version the version that the target catalog should
     * have
     */
    public ?\Mwb\Grt\Version $targetVersion = null;

    /**
     * @var $ignoreList ?\ArrayObject list of objects that should not be migrated in
     * the form objecttype:schemaname.objectname
     */
    public ?\ArrayObject $ignoreList = null;

    /**
     * @var $sourceObjects ?\ArrayObject temporary list of objects that should be
     * migrated
     */
    public ?\ArrayObject $sourceObjects = null;

    /**
     * @var $objectMigrationParams ?\ArrayObject the dictionary of parameters used
     * during object migration
     */
    public ?\ArrayObject $objectMigrationParams = null;

    /**
     * @var $objectCreationParams ?\ArrayObject the dictionary of parameters used
     * during the object creation
     */
    public ?\ArrayObject $objectCreationParams = null;

    /**
     * @var $dataBulkTransferParams ?\ArrayObject the dictionary of parameters used
     * during the bulk data transfer
     */
    public ?\ArrayObject $dataBulkTransferParams = null;

    /**
     * @var $migrationLog ?\ArrayObject a listing of log messages generated during
     * object migration
     */
    public ?\ArrayObject $migrationLog = null;

    /**
     * @var $creationLog ?\ArrayObject a listing of log messages generated during
     * object creation
     */
    public ?\ArrayObject $creationLog = null;

    /**
     * @var $dataTransferLog ?\ArrayObject a listing of log messages generated during
     * data transfer
     */
    public ?\ArrayObject $dataTransferLog = null;

    /**
     * @var $genericDatatypeMappings ?\ArrayObject datatype mapping for generic
     * migration
     */
    public ?\ArrayObject $genericDatatypeMappings = null;

    /**
     * @var $defaultColumnValueMappings ?\ArrayObject a mapping of default column
     * values for the selected source RDBMS. Default values that match one of the
     * values in the dict will be automatically translated.
     */
    public ?\ArrayObject $defaultColumnValueMappings = null;

    /**
     * @var $applicationData ?\ArrayObject internal parameters set by the migration
     * tool
     */
    public ?\ArrayObject $applicationData = null;
}

