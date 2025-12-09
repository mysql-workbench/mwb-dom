<?php

namespace Mwb\Grt\Db\Mysql;

class ForeignKey extends \Mwb\Grt\Db\ForeignKey
{
    /**
     * @var $referencedTable ?\Mwb\Grt\Db\Mysql\Table
     */
    public ?\Mwb\Grt\Db\Table $referencedTable = null;
}

