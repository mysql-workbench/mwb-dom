<?php

namespace Mwb\Grt\Db;

/**
 * an object used for object changes
 */
class DatabaseSyncObject extends \Mwb\Grt_Object
{
    /**
     * @var $changed ?int if set to 1 the object has been modified
     */
    public ?int $changed = null;

    /**
     * @var $alterDirection ?int 0 to apply the change to the database, 1 to apply the
     * change to the model
     */
    public ?int $alterDirection = null;

    /**
     * @var $modelObject ?\Mwb\Grt\NamedObject reference to the model object, empty if
     * this is a new object in the database
     */
    public ?\Mwb\Grt\NamedObject $modelObject = null;

    /**
     * @var $dbObject ?\Mwb\Grt\NamedObject reference to the database object, empty if
     * this is a new object in the model
     */
    public ?\Mwb\Grt\NamedObject $dbObject = null;

    /**
     * @var $children ?\ArrayObject
     */
    public ?\ArrayObject $children = null;

    /**
     * @var $syncLog ?\ArrayObject a listing of log messages generated during object
     * synchronization
     */
    public ?\ArrayObject $syncLog = null;
}

