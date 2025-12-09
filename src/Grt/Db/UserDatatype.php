<?php

namespace Mwb\Grt\Db;

class UserDatatype extends \Mwb\Grt_Object
{
    /**
     * @var $sqlDefinition ?string
     */
    public ?string $sqlDefinition = null;

    /**
     * @var $flags ?string
     */
    public ?string $flags = null;

    /**
     * @var $actualType ?\Mwb\Grt\Db\SimpleDatatype
     */
    public ?\Mwb\Grt\Db\SimpleDatatype $actualType = null;
}

