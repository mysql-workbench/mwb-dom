<?php

namespace Mwb\Grt\Db\Mgmt;

use \ArrayObject;

class Management extends \Mwb\Grt_Object
{
    public ?\ArrayObject $datatypeGroups = null;

    public ?\ArrayObject $otherStoredConns = null;

    public ?\ArrayObject $rdbms = null;

    public ?\ArrayObject $storedConns = null;

    public ?\ArrayObject $storedInstances = null;
}

