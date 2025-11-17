<?php

namespace Mwb\Grt\Model;

use \ArrayObject;

class Layer extends \Mwb\Grt\Model_Object
{
    public ?string $color = null;

    public ?string $description = null;

    public ?\ArrayObject $figures = null;

    public ?\ArrayObject $groups = null;

    public ?float $height = null;

    public ?float $left = null;

    public ?\ArrayObject $subLayers = null;

    public ?float $top = null;

    public ?float $width = null;
}

