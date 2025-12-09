<?php

namespace Mwb\Grt\Workbench\Logical;

/**
 * a logical model holding diagrams
 */
class Model extends \Mwb\Grt\Model\Model
{
    /**
     * @var $diagrams \Mwb\Grt\Workbench\Logical\Diagram[] the list of all available
     * diagrams
     */
    public ?\ArrayObject $diagrams = null;
}

