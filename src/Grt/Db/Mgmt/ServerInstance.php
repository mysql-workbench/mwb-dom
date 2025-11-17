<?php

namespace Mwb\Grt\Db\Mgmt;

use \Mwb\Grt\Db\Mgmt\Connection;
use \ArrayObject;

class ServerInstance extends \Mwb\Grt_Object
{
    public ?\Mwb\Grt\Db\Mgmt\Connection $connection = null;

    public ?\ArrayObject $loginInfo = null;

    public ?\ArrayObject $serverInfo = null;
}

