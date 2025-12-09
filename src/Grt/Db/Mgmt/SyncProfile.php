<?php

namespace Mwb\Grt\Db\Mgmt;

/**
 * DB synchronization profile containing a list last known names for each model
 * object in a equivalent schema in the server
 */
class SyncProfile extends \Mwb\Grt_Object
{
    /**
     * @var $targetHostIdentifier ?string identifier for the target DB server
     */
    public ?string $targetHostIdentifier = null;

    /**
     * @var $targetSchemaName ?string name of the target schema in the DB server
     */
    public ?string $targetSchemaName = null;

    /**
     * @var $lastKnownDBNames ?\ArrayObject dictionary of object-id to object name
     * values that were last seen in the target DB
     */
    public ?\ArrayObject $lastKnownDBNames = null;

    /**
     * @var $lastKnownViewDefinitions ?\ArrayObject dictionary of view object-id to the
     * checksums of the view definitions in both model and server (object-id:model,
     * object-id:server). The canonical location for these values in the object is in
     * oldServerSqlDefinition and oldModelSqlDefinition.
     */
    public ?\ArrayObject $lastKnownViewDefinitions = null;

    /**
     * @var $lastSyncDate ?string last date/time that the model was synchronized to
     * this target
     */
    public ?string $lastSyncDate = null;
}

