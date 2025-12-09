<?php

namespace Mwb\Grt\Db\Oracle;

/**
 * an Oracle database schema
 */
class Schema extends \Mwb\Grt\Db\Schema
{
    /**
     * @var $tables \Mwb\Grt\Db\Oracle\Table[]
     */
    public ?\ArrayObject $tables = null;

    /**
     * @var $views \Mwb\Grt\Db\Oracle\View[]
     */
    public ?\ArrayObject $views = null;

    /**
     * @var $routines \Mwb\Grt\Db\Oracle\Routine[]
     */
    public ?\ArrayObject $routines = null;

    /**
     * @var $routineGroups \Mwb\Grt\Db\Oracle\RoutineGroup[]
     */
    public ?\ArrayObject $routineGroups = null;

    /**
     * @var $synonyms \Mwb\Grt\Db\Oracle\Synonym[]
     */
    public ?\ArrayObject $synonyms = null;

    /**
     * @var $structuredTypes \Mwb\Grt\Db\Oracle\StructuredDatatype[]
     */
    public ?\ArrayObject $structuredTypes = null;

    /**
     * @var $sequences \Mwb\Grt\Db\Oracle\Sequence[]
     */
    public ?\ArrayObject $sequences = null;
}

