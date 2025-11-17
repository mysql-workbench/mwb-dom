<?php

namespace Mwb\Grt\Meta;

use \Mwb\Grt\Db\DatabaseObject;

class TaggedObject extends \Mwb\Grt_Object
{
    public ?string $description = null;

    public ?\Mwb\Grt\Db\DatabaseObject $object = null;
}

