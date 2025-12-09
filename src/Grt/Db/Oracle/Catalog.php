<?php

namespace Mwb\Grt\Db\Oracle;

/**
 * an Oracle database catalog
 */
class Catalog extends \Mwb\Grt\Db\Catalog
{
    /**
     * @var $logFileGroups \Mwb\Grt\Db\Oracle\LogFileGroup[]
     */
    public ?\ArrayObject $logFileGroups = null;

    /**
     * @var $tablespaces \Mwb\Grt\Db\Oracle\Tablespace[]
     */
    public ?\ArrayObject $tablespaces = null;

    /**
     * @var $serverLinks \Mwb\Grt\Db\Oracle\ServerLink[]
     */
    public ?\ArrayObject $serverLinks = null;

    /**
     * @var $schemata \Mwb\Grt\Db\Oracle\Schema[]
     */
    public ?\ArrayObject $schemata = null;
}

