<?php

namespace Mwb\Grt\Model;

use \ArrayObject;
use \Mwb\Grt\Model\Layer;

class Diagram extends \Mwb\Grt_Object
{
    public ?int $closed = null;

    public ?\ArrayObject $connections = null;

    public ?string $description = null;

    public ?\ArrayObject $figures = null;

    public ?float $height = null;

    public ?\ArrayObject $layers = null;

    public ?\ArrayObject $options = null;

    public ?\Mwb\Grt\Model\Layer $rootLayer = null;

    public ?\ArrayObject $selection = null;

    public ?int $updateBlocked = null;

    public ?float $width = null;

    public ?float $x = null;

    public ?float $y = null;

    public ?float $zoom = null;
}

