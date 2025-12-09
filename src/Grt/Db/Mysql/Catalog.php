<?php

namespace Mwb\Grt\Db\Mysql;

class Catalog extends \Mwb\Grt\Db\Catalog
{
    /**
     * @var $logFileGroups \Mwb\Grt\Db\Mysql\LogFileGroup[]
     */
    public ?\ArrayObject $logFileGroups = null;

    /**
     * @var $tablespaces \Mwb\Grt\Db\Mysql\Tablespace[]
     */
    public ?\ArrayObject $tablespaces = null;

    /**
     * @var $serverLinks \Mwb\Grt\Db\Mysql\ServerLink[]
     */
    public ?\ArrayObject $serverLinks = null;

    /**
     * @var $schemata \Mwb\Grt\Db\Mysql\Schema[]
     */
    public ?\ArrayObject $schemata = null;
}

