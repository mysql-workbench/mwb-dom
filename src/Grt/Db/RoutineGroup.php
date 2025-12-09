<?php

namespace Mwb\Grt\Db;

/**
 * a logical group of routines
 */
class RoutineGroup extends \Mwb\Grt\Db\DatabaseObject
{
    /**
     * @var $routines ?\ArrayObject
     */
    public ?\ArrayObject $routines = null;

    /**
     * @var $routineExpandedStates ?\ArrayObject specifies if the n-th routine is
     * expanded in the editor, 0 if collapsed
     */
    public ?\ArrayObject $routineExpandedStates = null;

    /**
     * @var $routineExpandedHeights ?\ArrayObject specifies the n-th routine height in
     * the editor, 0 for automatic height
     */
    public ?\ArrayObject $routineExpandedHeights = null;
}

