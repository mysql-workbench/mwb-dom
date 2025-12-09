<?php

namespace Mwb\Grt\Workbench\Physical;

/**
 * a model figure representing a collection of routines
 */
class RoutineGroupFigure extends \Mwb\Grt\Model\Figure
{
    /**
     * @var $routineGroup ?\Mwb\Grt\Db\RoutineGroup the routine group this figure
     * represents
     */
    public ?\Mwb\Grt\Db\RoutineGroup $routineGroup = null;
}

