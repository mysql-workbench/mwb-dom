<?php

namespace Mwb\Grt\Db\Mgmt;

/**
 * information about a Python DB 2.0 API compliant driver
 */
class PythonDBAPIDriver extends \Mwb\Grt\Db\Mgmt\Driver
{
    /**
     * @var $connectionStringTemplate ?string the template used to build the connection
     * parameter
     */
    public ?string $connectionStringTemplate = null;
}

