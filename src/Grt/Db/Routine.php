<?php

namespace Mwb\Grt\Db;

/**
 * an object that stores information about a database schema routine
 */
class Routine extends \Mwb\Grt\Db\DatabaseDdlObject
{
    /**
     * @var $name string the current name of the object
     */
    public ?string $name = null;

    /**
     * @var $routineType ?string
     */
    public ?string $routineType = null;

    /**
     * @var $sequenceNumber ?int defines position in editor
     */
    public ?int $sequenceNumber = null;
}

