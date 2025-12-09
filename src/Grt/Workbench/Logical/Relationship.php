<?php

namespace Mwb\Grt\Workbench\Logical;

/**
 * a model figure representing a relationship
 */
class Relationship extends \Mwb\Grt\Model\Figure
{
    /**
     * @var $relationship ?\Mwb\Grt\Eer\Relationship the relationship that is
     * represented
     */
    public ?\Mwb\Grt\Eer\Relationship $relationship = null;

    /**
     * @var $attributesExpanded ?int indicates whether the columns list is expanded
     */
    public ?int $attributesExpanded = null;
}

