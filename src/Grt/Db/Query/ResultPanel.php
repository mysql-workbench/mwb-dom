<?php

namespace Mwb\Grt\Db\Query;

/**
 * the GUI object that holds a query resultset and other related things
 */
class ResultPanel extends \Mwb\Grt_Object
{
    /**
     * @var $resultset ?\Mwb\Grt\Db\Query\Resultset the resultset grid. May be NULL
     */
    public ?\Mwb\Grt\Db\Query\Resultset $resultset = null;

    /**
     * @var $dockingPoint ?\Mwb\Grt\Mforms\ObjectReference docking point for plugins to
     * insert new tabs. The string argument of dock_view must point to an icon file.
     */
    public ?\Mwb\Grt\Mforms\ObjectReference $dockingPoint = null;
}

