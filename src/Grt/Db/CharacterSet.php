<?php

namespace Mwb\Grt\Db;

use \ArrayObject;

class CharacterSet extends \Mwb\Grt_Object
{
    public ?\ArrayObject $collations = null;

    public ?string $defaultCollation = null;

    public ?string $description = null;
}

