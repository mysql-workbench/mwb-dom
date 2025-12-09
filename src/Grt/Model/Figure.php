<?php

namespace Mwb\Grt\Model;

/**
 * a single model figure
 */
class Figure extends \Mwb\Grt\Model_Object
{
    /**
     * @var $expanded ?int
     */
    public ?int $expanded = null;

    /**
     * @var $group ?\Mwb\Grt\Model\Group
     */
    public ?\Mwb\Grt\Model\Group $group = null;

    /**
     * @var $height ?float
     */
    public ?float $height = null;

    /**
     * @var $layer ?\Mwb\Grt\Model\Layer
     */
    public ?\Mwb\Grt\Model\Layer $layer = null;

    /**
     * @var $left ?float
     */
    public ?float $left = null;

    /**
     * @var $locked ?int
     */
    public ?int $locked = null;

    /**
     * @var $top ?float
     */
    public ?float $top = null;

    /**
     * @var $width ?float
     */
    public ?float $width = null;

    /**
     * @var $manualSizing ?int manually resize object
     */
    public ?int $manualSizing = null;

    /**
     * @var $color ?string color style name for the figure
     */
    public ?string $color = null;
}

