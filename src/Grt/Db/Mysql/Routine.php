<?php

namespace Mwb\Grt\Db\Mysql;

use \ArrayObject;

class Routine extends \Mwb\Grt\Db\Routine
{
    public ?\ArrayObject $params = null;

    public ?string $returnDatatype = null;

    public ?string $security = null;
}

