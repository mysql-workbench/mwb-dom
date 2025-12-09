<?php

namespace Mwb\Grt\Db\Mysql;

class Tablespace extends \Mwb\Grt\Db\Tablespace
{
    /**
     * @var $engine ?string NDB and InnoDB are supported
     */
    public ?string $engine = null;

    /**
     * @var $nodeGroupId ?int the same id as used for a logfile group
     */
    public ?int $nodeGroupId = null;

    /**
     * @var $wait ?int no documentation yet
     */
    public ?int $wait = null;
}

