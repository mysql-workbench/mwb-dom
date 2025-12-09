<?php

namespace Mwb\Grt\Db;

class DatabaseDdlObject extends \Mwb\Grt\Db\DatabaseObject
{
    /**
     * @var $definer ?string
     */
    public ?string $definer = null;

    /**
     * @var $sqlBody ?string
     */
    public ?string $sqlBody = null;

    /**
     * @var $sqlDefinition ?string
     */
    public ?string $sqlDefinition = null;
}

