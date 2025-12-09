<?php

namespace Mwb\Grt\Db\Mssql;

class Schema extends \Mwb\Grt\Db\Schema
{
    /**
     * @var $tables \Mwb\Grt\Db\Mssql\Table[]
     */
    public ?\ArrayObject $tables = null;

    /**
     * @var $views \Mwb\Grt\Db\Mssql\View[]
     */
    public ?\ArrayObject $views = null;

    /**
     * @var $routines \Mwb\Grt\Db\Mssql\Routine[]
     */
    public ?\ArrayObject $routines = null;

    /**
     * @var $routineGroups \Mwb\Grt\Db\Mssql\RoutineGroup[]
     */
    public ?\ArrayObject $routineGroups = null;

    /**
     * @var $synonyms \Mwb\Grt\Db\Mssql\Synonym[]
     */
    public ?\ArrayObject $synonyms = null;

    /**
     * @var $structuredTypes \Mwb\Grt\Db\Mssql\StructuredDatatype[]
     */
    public ?\ArrayObject $structuredTypes = null;

    /**
     * @var $sequences \Mwb\Grt\Db\Mssql\Sequence[]
     */
    public ?\ArrayObject $sequences = null;
}

