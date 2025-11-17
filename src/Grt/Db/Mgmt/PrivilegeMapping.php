<?php

namespace Mwb\Grt\Db\Mgmt;

use \ArrayObject;

class PrivilegeMapping extends \Mwb\Grt_Object
{
    public ?\ArrayObject $privileges = null;

    public ?string $structName = null;
}

