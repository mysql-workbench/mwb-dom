<?php

namespace Mwb\Grt\Eer;

class Attribute extends \Mwb\Grt\Eer\Object
{
    /**
     * @var $datatype ?\Mwb\Grt\Eer\Datatype
     */
    public ?\Mwb\Grt\Eer\Datatype $datatype = null;

    /**
     * @var $isIdentifying ?int specifies if this attribute is an identifying attribute
     * for the entity
     */
    public ?int $isIdentifying = null;
}

