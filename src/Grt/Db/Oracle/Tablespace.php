<?php

namespace Mwb\Grt\Db\Oracle;

class Tablespace extends \Mwb\Grt\Db\Tablespace
{
    /**
     * @var $autoExtend ?int
     */
    public ?int $autoExtend = null;

    /**
     * @var $maxSize ?int
     */
    public ?int $maxSize = null;

    /**
     * @var $logging ?int
     */
    public ?int $logging = null;

    /**
     * @var $online ?int
     */
    public ?int $online = null;

    /**
     * @var $temporary ?int
     */
    public ?int $temporary = null;

    /**
     * @var $default ?string
     */
    public ?string $default = null;
}

