<?php

namespace Mwb\Grt\Db\Mgmt;

/**
 * specifies which privileges are available for this object type
 */
class PrivilegeMapping extends \Mwb\Grt_Object
{
    /**
     * @var $structName ?string the struct of the database object
     */
    public ?string $structName = null;

    /**
     * @var $privileges ?\ArrayObject the list of available privileges for this object
     * type
     */
    public ?\ArrayObject $privileges = null;
}

