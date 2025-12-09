<?php

namespace Mwb\Grt;

/**
 * Named Object
 *
 * an object that tracks name changes
 */
class NamedObject extends \Mwb\Grt_Object
{
    /**
     * @var string $name the current name of the object
     */
    public ?string $name = null;

    /**
     * @var string $oldName used to keep track of the old, original name of the object
     * if the object gets renamed
     */
    public ?string $oldName = null;

    /**
     * @var string $comment a text describing the object
     */
    public ?string $comment = null;
}

