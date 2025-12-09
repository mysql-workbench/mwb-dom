<?php

namespace Mwb\Grt\Db;

class Role extends \Mwb\Grt\Db\DatabaseObject
{
    /**
     * @var $childRoles ?\ArrayObject the list of roles that derive from this one. They
     * will have all privileges from this role and it's parents.
     */
    public ?\ArrayObject $childRoles = null;

    /**
     * @var $parentRole ?\Mwb\Grt\Db\Role role that this role derives from or empty if
     * there is no parent role. All privileges which has parent present for current
     * Role
     */
    public ?\Mwb\Grt\Db\Role $parentRole = null;

    /**
     * @var $privileges ?\ArrayObject the list of privileges available for this role
     */
    public ?\ArrayObject $privileges = null;
}

