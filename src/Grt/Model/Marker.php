<?php

namespace Mwb\Grt\Model;

use \Mwb\Grt_Object;

class Marker extends \Mwb\Grt_Object
{
    public ?\Mwb\Grt_Object $diagram = null;

    public ?float $x = null;

    public ?float $y = null;

    public ?float $zoom = null;
}

