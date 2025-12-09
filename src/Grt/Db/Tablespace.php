<?php

namespace Mwb\Grt\Db;

class Tablespace extends \Mwb\Grt\Db\DatabaseObject
{
    /**
     * @var $dataFile ?string
     */
    public ?string $dataFile = null;

    /**
     * @var $initialSize ?int
     */
    public ?int $initialSize = null;

    /**
     * @var $extentSize ?int
     */
    public ?int $extentSize = null;

    /**
     * @var $autoExtendSize ?int
     */
    public ?int $autoExtendSize = null;

    /**
     * @var $maxSize ?int
     */
    public ?int $maxSize = null;

    /**
     * @var $logFileGroup ?\Mwb\Grt\Db\LogFileGroup the log file group that is used for
     * this tablespace
     */
    public ?\Mwb\Grt\Db\LogFileGroup $logFileGroup = null;
}

