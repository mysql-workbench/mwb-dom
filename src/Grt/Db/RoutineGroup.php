<?php

namespace Mwb\Grt\Db;

use \ArrayObject;

class RoutineGroup extends \Mwb\Grt\Db\DatabaseObject
{
    public ?\ArrayObject $routineExpandedHeights = null;

    public ?\ArrayObject $routineExpandedStates = null;

    public ?\ArrayObject $routines = null;
}

