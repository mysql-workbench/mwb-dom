<?php

namespace Mwb\Grt\Db\Migration;

class MigrationParameter extends \Mwb\Grt_Object
{
    /**
     * @var $caption ?string
     */
    public ?string $caption = null;

    /**
     * @var $description ?string
     */
    public ?string $description = null;

    /**
     * @var $paramType ?string one of string, boolean
     */
    public ?string $paramType = null;

    /**
     * @var $defaultValue ?string
     */
    public ?string $defaultValue = null;
}

