<?php

namespace Mwb\Grt\Db;

class Schema extends \Mwb\Grt\Db\DatabaseObject
{
    /**
     * @var $defaultCharacterSetName ?string
     */
    public ?string $defaultCharacterSetName = null;

    /**
     * @var $defaultCollationName ?string
     */
    public ?string $defaultCollationName = null;

    /**
     * @var $tables ?\ArrayObject
     */
    public ?\ArrayObject $tables = null;

    /**
     * @var $views ?\ArrayObject
     */
    public ?\ArrayObject $views = null;

    /**
     * @var $routines ?\ArrayObject
     */
    public ?\ArrayObject $routines = null;

    /**
     * @var $routineGroups ?\ArrayObject
     */
    public ?\ArrayObject $routineGroups = null;

    /**
     * @var $synonyms ?\ArrayObject
     */
    public ?\ArrayObject $synonyms = null;

    /**
     * @var $structuredTypes ?\ArrayObject
     */
    public ?\ArrayObject $structuredTypes = null;

    /**
     * @var $sequences ?\ArrayObject
     */
    public ?\ArrayObject $sequences = null;

    /**
     * @var $events ?\ArrayObject
     */
    public ?\ArrayObject $events = null;
}

