<?php

namespace Mwb\Grt\Db\Mysql;

class Routine extends \Mwb\Grt\Db\Routine
{
    /**
     * @var $security ?string
     */
    public ?string $security = null;

    /**
     * @var $returnDatatype ?string
     */
    public ?string $returnDatatype = null;

    /**
     * @var $params ?\ArrayObject
     */
    public ?\ArrayObject $params = null;
}

