<?php

namespace Mwb\Grt\Meta;

class TaggedObject extends \Mwb\Grt_Object
{
    /**
     * @var $object ?\Mwb\Grt\Db\DatabaseObject
     */
    public ?\Mwb\Grt\Db\DatabaseObject $object = null;

    /**
     * @var $description ?string
     */
    public ?string $description = null;
}

