<?php

namespace Mwb\Grt\Eer;

class Relationship extends \Mwb\Grt\Eer\Object
{
    /**
     * @var $startMandatory ?int mandatory in the source table
     */
    public ?int $startMandatory = null;

    /**
     * @var $endMandatory ?int mandatory in the target table
     */
    public ?int $endMandatory = null;

    /**
     * @var $attribute ?\ArrayObject
     */
    public ?\ArrayObject $attribute = null;
}

