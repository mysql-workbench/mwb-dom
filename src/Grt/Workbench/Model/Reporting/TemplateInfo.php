<?php

namespace Mwb\Grt\Workbench\Model\Reporting;

use \ArrayObject;

class TemplateInfo extends \Mwb\Grt_Object
{
    public ?string $description = null;

    public ?string $mainFileName = null;

    public ?\ArrayObject $styles = null;
}

