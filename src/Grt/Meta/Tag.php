<?php

namespace Mwb\Grt\Meta;

class Tag extends \Mwb\Grt_Object
{
    /**
     * @var $category ?\Mwb\Grt_Object
     */
    public ?\Mwb\Grt_Object $category = null;

    /**
     * @var $color ?string
     */
    public ?string $color = null;

    /**
     * @var $label ?string
     */
    public ?string $label = null;

    /**
     * @var $description ?string
     */
    public ?string $description = null;

    /**
     * @var $objects ?\ArrayObject
     */
    public ?\ArrayObject $objects = null;
}

