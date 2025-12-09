<?php

namespace Mwb\Grt\Model;

/**
 * a layer that contains figure
 */
class Layer extends \Mwb\Grt\Model_Object
{
    /**
     * @var $color ?string
     */
    public ?string $color = null;

    /**
     * @var $description ?string
     */
    public ?string $description = null;

    /**
     * @var $figures ?\ArrayObject list of figures in layer, in stacking order. Lower
     * elements come first.
     */
    public ?\ArrayObject $figures = null;

    /**
     * @var $groups ?\ArrayObject
     */
    public ?\ArrayObject $groups = null;

    /**
     * @var $height ?float
     */
    public ?float $height = null;

    /**
     * @var $left ?float
     */
    public ?float $left = null;

    /**
     * @var $subLayers ?\ArrayObject
     */
    public ?\ArrayObject $subLayers = null;

    /**
     * @var $top ?float
     */
    public ?float $top = null;

    /**
     * @var $width ?float
     */
    public ?float $width = null;
}

