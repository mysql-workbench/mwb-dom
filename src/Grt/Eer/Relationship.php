<?php

namespace Mwb\Grt\Eer;

use \ArrayObject;

class Relationship extends \Mwb\Grt\Eer\Object
{
    public ?\ArrayObject $attribute = null;

    public ?int $endMandatory = null;

    public ?int $startMandatory = null;
}

