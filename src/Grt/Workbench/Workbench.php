<?php

namespace Mwb\Grt\Workbench;

/**
 * an object to store the workbench's data
 */
class Workbench extends \Mwb\Grt\App\Application
{
    /**
     * @var $doc ?\Mwb\Grt\Workbench\Document the Workbench document
     */
    public ?\Mwb\Grt\App\Document $doc = null;

    /**
     * @var $docPath ?string the Workbench document path
     */
    public ?string $docPath = null;

    /**
     * @var $sqlEditors ?\ArrayObject list of open SQL Editor instances
     */
    public ?\ArrayObject $sqlEditors = null;

    /**
     * @var $rdbmsMgmt ?\Mwb\Grt\Db\Mgmt\Management the RDBMS management information
     */
    public ?\Mwb\Grt\Db\Mgmt\Management $rdbmsMgmt = null;

    /**
     * @var $migration ?\Mwb\Grt\Db\Migration\Migration data for Migration Plugin
     */
    public ?\Mwb\Grt\Db\Migration\Migration $migration = null;
}

