<?php

namespace Mwb\Grt\Db;

class RolePrivilege extends \Mwb\Grt_Object
{
    /**
     * @var $privileges ?\ArrayObject the privileges for the object, e.g. CREATE
     */
    public ?\ArrayObject $privileges = null;

    /**
     * @var $databaseObject ?\Mwb\Grt\Db\DatabaseObject the database object this
     * privilege is assigned to
     */
    public ?\Mwb\Grt\Db\DatabaseObject $databaseObject = null;

    /**
     * @var $databaseObjectType ?string specifies the type, e.g. TABLE, used when
     * wildcards are needed, like test.*
     */
    public ?string $databaseObjectType = null;

    /**
     * @var $databaseObjectName ?string used when wildcards are needed, like test.*
     */
    public ?string $databaseObjectName = null;
}

