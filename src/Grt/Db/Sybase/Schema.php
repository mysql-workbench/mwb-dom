<?php

namespace Mwb\Grt\Db\Sybase;

class Schema extends \Mwb\Grt\Db\Schema
{
    /**
     * @var $tables \Mwb\Grt\Db\Sybase\Table[]
     */
    public ?\ArrayObject $tables = null;

    /**
     * @var $views \Mwb\Grt\Db\Sybase\View[]
     */
    public ?\ArrayObject $views = null;

    /**
     * @var $routines \Mwb\Grt\Db\Sybase\Routine[]
     */
    public ?\ArrayObject $routines = null;

    /**
     * @var $routineGroups \Mwb\Grt\Db\Sybase\RoutineGroup[]
     */
    public ?\ArrayObject $routineGroups = null;

    /**
     * @var $synonyms \Mwb\Grt\Db\Sybase\Synonym[]
     */
    public ?\ArrayObject $synonyms = null;

    /**
     * @var $structuredTypes \Mwb\Grt\Db\Sybase\StructuredDatatype[]
     */
    public ?\ArrayObject $structuredTypes = null;

    /**
     * @var $sequences \Mwb\Grt\Db\Sybase\Sequence[]
     */
    public ?\ArrayObject $sequences = null;
}

