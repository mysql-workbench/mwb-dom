<?php

namespace Mwb\Grt\Db\Query;

class QueryEditor extends \Mwb\Grt\Db\Query\QueryBuffer
{
    /**
     * @var $resultPanels ?\ArrayObject list of open query result panels. Result panels
     * contain the resultset grid and other views
     */
    public ?\ArrayObject $resultPanels = null;

    /**
     * @var $activeResultPanel ?\Mwb\Grt\Db\Query\ResultPanel result panel that is
     * currently selected in UI
     */
    public ?\Mwb\Grt\Db\Query\ResultPanel $activeResultPanel = null;

    /**
     * @var $resultDockingPoint ?\Mwb\Grt\Mforms\ObjectReference
     */
    public ?\Mwb\Grt\Mforms\ObjectReference $resultDockingPoint = null;
}

