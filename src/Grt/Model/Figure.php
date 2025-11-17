<?php

namespace Mwb\Grt\Model;

use \Mwb\Grt\Model\Group;
use \Mwb\Grt\Model\Layer;

class Figure extends \Mwb\Grt\Model_Object
{
    public ?string $color = null;

    public ?int $expanded = null;

    public ?\Mwb\Grt\Model\Group $group = null;

    public ?float $height = null;

    public ?\Mwb\Grt\Model\Layer $layer = null;

    public ?float $left = null;

    public ?int $locked = null;

    public ?int $manualSizing = null;

    public ?float $top = null;

    public ?float $width = null;
}

