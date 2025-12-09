<?php

namespace Mwb\Grt\Db\Sybase;

class Column extends \Mwb\Grt\Db\Column
{
    /**
     * @var $identity ?int
     */
    public ?int $identity = null;

    /**
     * @var $computed ?int
     */
    public ?int $computed = null;
}

