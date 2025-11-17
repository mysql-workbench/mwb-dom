<?php

namespace Mwb\Grt\Model;

use \Mwb\Grt\Model\Diagram;
use \ArrayObject;

class Model extends \Mwb\Grt_Object
{
    public ?\Mwb\Grt\Model\Diagram $currentDiagram = null;

    public ?\ArrayObject $customData = null;

    public ?\ArrayObject $diagrams = null;

    public ?\ArrayObject $markers = null;

    public ?\ArrayObject $options = null;
}

