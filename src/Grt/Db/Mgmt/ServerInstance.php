<?php

namespace Mwb\Grt\Db\Mgmt;

/**
 * DB server connection and management information
 */
class ServerInstance extends \Mwb\Grt_Object
{
    /**
     * @var $connection ?\Mwb\Grt\Db\Mgmt\Connection
     */
    public ?\Mwb\Grt\Db\Mgmt\Connection $connection = null;

    /**
     * @var $loginInfo ?\ArrayObject login information to the server
     */
    public ?\ArrayObject $loginInfo = null;

    /**
     * @var $serverInfo ?\ArrayObject server configuration information
     */
    public ?\ArrayObject $serverInfo = null;
}

