<?php

namespace Mwb\Grt\Db;

use \Mwb\Grt\Db\DatabaseObject;

class Synonym extends \Mwb\Grt\Db\DatabaseObject
{
    public ?int $isPublic = null;

    public ?\Mwb\Grt\Db\DatabaseObject $referencedObject = null;

    public ?string $referencedObjectName = null;

    public ?string $referencedSchemaName = null;
}

