<?php

namespace Mwb\Grt\Workbench\Logical;

use \Mwb\Grt\Eer\Entity;

class Entity extends \Mwb\Grt\Model\Figure
{
    public ?int $attributesExpanded = null;

    public ?\Mwb\Grt\Eer\Entity $entity = null;
}

