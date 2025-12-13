<?php

namespace Mwb;

/**
 * the parent of all other objects
 */
class Grt_Object
{
    /**
     * @var string $name the object's name
     */
    public ?string $name = null;

    /**
     * @var \Mwb\Grt_Object $owner the object that owns this object
     */
    public ?\Mwb\Grt_Object $owner = null;

    public function __construct($options = [])
    {
        foreach ($options as $key => $value) {
            $this->$key = $value;
        }
    }
}

