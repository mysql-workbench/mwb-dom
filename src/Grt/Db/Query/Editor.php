<?php

namespace Mwb\Grt\Db\Query;

/**
 * a proxy to an instance of a connection to a DB server, equivalent to a SQL
 * Editor tab.\n This object cannot be instantiated directly.
 */
class Editor extends \Mwb\Grt_Object
{
    /**
     * @var $connection ?\Mwb\Grt\Db\Mgmt\Connection connection data
     */
    public ?\Mwb\Grt\Db\Mgmt\Connection $connection = null;

    /**
     * @var $isConnected ?int whether the editor is connected
     */
    public ?int $isConnected = null;

    /**
     * @var $queryEditors ?\ArrayObject list of open editor buffers. This list cannot
     * be modified
     */
    public ?\ArrayObject $queryEditors = null;

    /**
     * @var $activeQueryEditor ?\Mwb\Grt\Db\Query\QueryEditor query editor that is
     * currently selected
     */
    public ?\Mwb\Grt\Db\Query\QueryEditor $activeQueryEditor = null;

    /**
     * @var $defaultSchema ?string The default schema to use for queries (equivalent to
     * USE schema)
     */
    public ?string $defaultSchema = null;

    /**
     * @var $serverVersion ?\Mwb\Grt\Version
     */
    public ?\Mwb\Grt\Version $serverVersion = null;

    /**
     * @var $customData ?\ArrayObject
     */
    public ?\ArrayObject $customData = null;

    /**
     * @var $dockingPoint ?\Mwb\Grt\Mforms\ObjectReference
     */
    public ?\Mwb\Grt\Mforms\ObjectReference $dockingPoint = null;

    /**
     * @var $sidebar ?\Mwb\Grt\Mforms\ObjectReference
     */
    public ?\Mwb\Grt\Mforms\ObjectReference $sidebar = null;

    /**
     * @var $schemaTreeSelection ?\ArrayObject
     */
    public ?\ArrayObject $schemaTreeSelection = null;
}

