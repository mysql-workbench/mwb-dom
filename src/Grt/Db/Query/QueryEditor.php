<?php

namespace Mwb\Grt\Db\Query;

use \Mwb\Grt\Db\Query\ResultPanel;
use \ArrayObject;

class QueryEditor extends \Mwb\Grt\Db\Query\QueryBuffer
{
    public ?\Mwb\Grt\Db\Query\ResultPanel $activeResultPanel = null;

    public ?\ArrayObject $resultPanels = null;
}

