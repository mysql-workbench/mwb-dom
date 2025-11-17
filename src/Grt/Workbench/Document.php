<?php

namespace Mwb\Grt\Workbench;

//use \Mwb\Grt\Workbench\Logical\Model;
//use \Mwb\Grt\Model\Model;
use \ArrayObject;

class Document extends \Mwb\Grt\App\Document
{
    public ?\Mwb\Grt\Workbench\Logical\Model $logicalModel = null;

    public ?\Mwb\Grt\Model\Model $overviewCurrentModelType = null;

    public ?\ArrayObject $overviewPanels = null;

    public ?\ArrayObject $physicalModels = null;
}

