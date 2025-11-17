<?php

namespace Mwb\Grt\Workbench\Logical;

use \Mwb\Grt\Eer\Relationship;

class Relationship extends \Mwb\Grt\Model\Figure
{
    public ?int $attributesExpanded = null;

    public ?\Mwb\Grt\Eer\Relationship $relationship = null;
}

