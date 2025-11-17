<?php

namespace Mwb\Grt\Model;

use \Mwb\Grt\Model\Figure;

class Connection extends \Mwb\Grt\Model_Object
{
    public ?int $drawSplit = null;

    public ?\Mwb\Grt\Model\Figure $endFigure = null;

    public ?\Mwb\Grt\Model\Figure $startFigure = null;
}

