<?php

namespace Mwb\Grt\Db\Mysql;

class LogFileGroup extends \Mwb\Grt\Db\LogFileGroup
{
    /**
     * @var $engine ?string usually only NDB makes sense
     */
    public ?string $engine = null;

    /**
     * @var $nodeGroupId ?int a unique id for the group, used in a tablespace
     */
    public ?int $nodeGroupId = null;

    /**
     * @var $wait ?int no documentation yet
     */
    public ?int $wait = null;
}

