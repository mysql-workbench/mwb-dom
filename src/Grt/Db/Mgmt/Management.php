<?php

namespace Mwb\Grt\Db\Mgmt;

/**
 * Management for RDBMS drivers
 */
class Management extends \Mwb\Grt_Object
{
    /**
     * @var $rdbms ?\ArrayObject a list of Rdbms with available drivers
     */
    public ?\ArrayObject $rdbms = null;

    /**
     * @var $storedConns ?\ArrayObject a list of stored connections
     */
    public ?\ArrayObject $storedConns = null;

    /**
     * @var $storedInstances ?\ArrayObject a list of stored DB server instances
     */
    public ?\ArrayObject $storedInstances = null;

    /**
     * @var $otherStoredConns ?\ArrayObject a list of stored non-MySQL connections
     */
    public ?\ArrayObject $otherStoredConns = null;

    /**
     * @var $datatypeGroups ?\ArrayObject list of datatypegroups
     */
    public ?\ArrayObject $datatypeGroups = null;
}

