<?php

namespace Mwb\Grt\Workbench\Logical;

/**
 * a model figure representing a table
 */
class Entity extends \Mwb\Grt\Model\Figure
{
    /**
     * @var $entity ?\Mwb\Grt\Eer\Entity the entity this figure represents
     */
    public ?\Mwb\Grt\Eer\Entity $entity = null;

    /**
     * @var $attributesExpanded ?int indicates whether the columns list is expanded
     */
    public ?int $attributesExpanded = null;
}

