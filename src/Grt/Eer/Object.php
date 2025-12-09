<?php

namespace Mwb\Grt\Eer;

class Object extends \Mwb\Grt\NamedObject
{
    /**
     * @var $commentedOut ?int if set to 1 the object will be commented out but e.g.
     * still be written to scripts
     */
    public ?int $commentedOut = null;

    /**
     * @var $customData ?\ArrayObject
     */
    public ?\ArrayObject $customData = null;
}

