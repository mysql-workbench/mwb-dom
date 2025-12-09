<?php

namespace Mwb\Grt\Model;

/**
 * a marker storing the active diagram and position on the diagram
 */
class Marker extends \Mwb\Grt_Object
{
    /**
     * @var $diagram ?\Mwb\Grt_Object
     */
    public ?\Mwb\Grt_Object $diagram = null;

    /**
     * @var $x ?float
     */
    public ?float $x = null;

    /**
     * @var $y ?float
     */
    public ?float $y = null;

    /**
     * @var $zoom ?float
     */
    public ?float $zoom = null;
}

