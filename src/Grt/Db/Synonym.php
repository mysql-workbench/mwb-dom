<?php

namespace Mwb\Grt\Db;

class Synonym extends \Mwb\Grt\Db\DatabaseObject
{
    /**
     * @var $isPublic ?int
     */
    public ?int $isPublic = null;

    /**
     * @var $referencedObject ?\Mwb\Grt\Db\DatabaseObject
     */
    public ?\Mwb\Grt\Db\DatabaseObject $referencedObject = null;

    /**
     * @var $referencedSchemaName ?string
     */
    public ?string $referencedSchemaName = null;

    /**
     * @var $referencedObjectName ?string
     */
    public ?string $referencedObjectName = null;
}

