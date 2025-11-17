<?php

namespace Mwb\Grt\Model;

use \ArrayObject;

class Group extends \Mwb\Grt_Object
{
    public ?string $description = null;

    public ?\ArrayObject $figures = null;

    public ?\ArrayObject $subGroups = null;
}

