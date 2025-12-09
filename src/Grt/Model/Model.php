<?php

namespace Mwb\Grt\Model;

class Model extends \Mwb\Grt_Object
{
    /**
     * @var $currentDiagram ?\Mwb\Grt\Model\Diagram the currently active diagram
     */
    public ?\Mwb\Grt\Model\Diagram $currentDiagram = null;

    /**
     * @var $diagrams ?\ArrayObject the list of all available diagrams
     */
    public ?\ArrayObject $diagrams = null;

    /**
     * @var $markers ?\ArrayObject a list of markers that can be used to jump to a
     * given diagram at a given position
     */
    public ?\ArrayObject $markers = null;

    /**
     * @var $options ?\ArrayObject model specific options
     */
    public ?\ArrayObject $options = null;

    /**
     * @var $customData ?\ArrayObject
     */
    public ?\ArrayObject $customData = null;
}

