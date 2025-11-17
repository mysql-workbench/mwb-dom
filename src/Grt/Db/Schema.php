<?php

namespace Mwb\Grt\Db;

use \ArrayObject;

class Schema extends \Mwb\Grt\Db\DatabaseObject
{
    public ?string $defaultCharacterSetName = null;

    public ?string $defaultCollationName = null;

    public ?\ArrayObject $events = null;

    public ?\ArrayObject $routineGroups = null;

    public ?\ArrayObject $routines = null;

    public ?\ArrayObject $sequences = null;

    public ?\ArrayObject $structuredTypes = null;

    public ?\ArrayObject $synonyms = null;

    public ?\ArrayObject $tables = null;

    public ?\ArrayObject $views = null;
}

