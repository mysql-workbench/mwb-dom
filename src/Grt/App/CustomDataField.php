<?php

namespace Mwb\Grt\App;

class CustomDataField extends \Mwb\Grt_Object
{
    /**
     * @var $objectStruct ?string object struct names that this applies to
     */
    public ?string $objectStruct = null;

    /**
     * @var $type ?string type of the field (int, string, double, dict, object, list)
     */
    public ?string $type = null;

    /**
     * @var $defaultValue ?string default value for the field
     */
    public ?string $defaultValue = null;

    /**
     * @var $description ?string description of the field
     */
    public ?string $description = null;
}

