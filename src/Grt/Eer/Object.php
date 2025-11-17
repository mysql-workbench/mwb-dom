<?php

namespace Mwb\Grt\Eer;

use \ArrayObject;

class Object extends \Mwb\Grt\NamedObject
{
    public ?int $commentedOut = null;

    public ?\ArrayObject $customData = null;
}

