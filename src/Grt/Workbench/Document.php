<?php

namespace Mwb\Grt\Workbench;

class Document extends \Mwb\Grt\App\Document
{
    /**
     * @var $logicalModel ?\Mwb\Grt\Workbench\Logical\Model the logical model
     */
    public ?\Mwb\Grt\Workbench\Logical\Model $logicalModel = null;

    /**
     * @var $physicalModels ?\ArrayObject the physical models
     */
    public ?\ArrayObject $physicalModels = null;

    /**
     * @var $overviewCurrentModelType ?\Mwb\Grt\Model\Model specifies if the panel is
     * currently selected
     */
    public ?\Mwb\Grt\Model\Model $overviewCurrentModelType = null;

    /**
     * @var $overviewPanels ?\ArrayObject the panels that are presented on the overview
     * page
     */
    public ?\ArrayObject $overviewPanels = null;
}

