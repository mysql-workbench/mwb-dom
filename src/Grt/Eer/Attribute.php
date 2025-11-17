<?php

namespace Mwb\Grt\Eer;

use \Mwb\Grt\Eer\Datatype;

class Attribute extends \Mwb\Grt\Eer\Object
{
    public ?\Mwb\Grt\Eer\Datatype $datatype = null;

    public ?int $isIdentifying = null;
}

