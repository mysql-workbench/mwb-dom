<?php

namespace Mwb\Grt\Model;

/**
 * a group of figures
 */
class Group extends \Mwb\Grt_Object
{
    /**
     * @var $owner ?\Mwb\Grt\Model\Diagram
     */
    public ?\Mwb\Grt_Object $owner = null;

    /**
     * @var $description ?string
     */
    public ?string $description = null;

    /**
     * @var $figures ?\ArrayObject
     */
    public ?\ArrayObject $figures = null;

    /**
     * @var $subGroups ?\ArrayObject
     */
    public ?\ArrayObject $subGroups = null;
}

