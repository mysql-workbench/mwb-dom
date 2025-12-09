<?php

namespace Mwb\Grt\Db\Mysql;

class Schema extends \Mwb\Grt\Db\Schema
{
    /**
     * @var $tables \Mwb\Grt\Db\Mysql\Table[]
     */
    public ?\ArrayObject $tables = null;

    /**
     * @var $views \Mwb\Grt\Db\Mysql\View[]
     */
    public ?\ArrayObject $views = null;

    /**
     * @var $routines \Mwb\Grt\Db\Mysql\Routine[]
     */
    public ?\ArrayObject $routines = null;

    /**
     * @var $routineGroups \Mwb\Grt\Db\Mysql\RoutineGroup[]
     */
    public ?\ArrayObject $routineGroups = null;

    /**
     * @var $synonyms \Mwb\Grt\Db\Mysql\Synonym[]
     */
    public ?\ArrayObject $synonyms = null;

    /**
     * @var $structuredTypes \Mwb\Grt\Db\Mysql\StructuredDatatype[]
     */
    public ?\ArrayObject $structuredTypes = null;

    /**
     * @var $sequences \Mwb\Grt\Db\Mysql\Sequence[]
     */
    public ?\ArrayObject $sequences = null;
}

