<?php

namespace Mwb\Grt\Db;

class User extends \Mwb\Grt\Db\DatabaseObject
{
    /**
     * @var $password ?string the password assigned to the user
     */
    public ?string $password = null;

    /**
     * @var $roles ?\ArrayObject the list of assigned roles
     */
    public ?\ArrayObject $roles = null;
}

