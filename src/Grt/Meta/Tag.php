<?php

namespace Mwb\Grt\Meta;

use \Mwb\Grt_Object;
use \ArrayObject;

class Tag extends \Mwb\Grt_Object
{
    public ?\Mwb\Grt_Object $category = null;

    public ?string $color = null;

    public ?string $description = null;

    public ?string $label = null;

    public ?\ArrayObject $objects = null;
}

