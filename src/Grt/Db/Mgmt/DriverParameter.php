<?php

namespace Mwb\Grt\Db\Mgmt;

use \ArrayObject;

class DriverParameter extends \Mwb\Grt_Object
{
    public ?string $caption = null;

    public ?string $defaultValue = null;

    public ?string $description = null;

    public ?int $layoutAdvanced = null;

    public ?int $layoutRow = null;

    public ?int $layoutWidth = null;

    public ?string $lookupValueMethod = null;

    public ?string $lookupValueModule = null;

    public ?string $paramType = null;

    public ?\ArrayObject $paramTypeDetails = null;

    public ?int $required = null;
}

