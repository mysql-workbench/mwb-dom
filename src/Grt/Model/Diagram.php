<?php

namespace Mwb\Grt\Model;

/**
 * a diagram of the model data
 */
class Diagram extends \Mwb\Grt_Object
{
    /**
     * @var $owner ?\Mwb\Grt\Model\Model
     */
    public ?\Mwb\Grt_Object $owner = null;

    /**
     * @var $name string the current name of the object
     */
    public ?string $name = null;

    /**
     * @var $description ?string
     */
    public ?string $description = null;

    /**
     * @var $figures ?\ArrayObject all figures displayed in this diagram
     */
    public ?\ArrayObject $figures = null;

    /**
     * @var $connections ?\ArrayObject all connections displayed in this diagram
     */
    public ?\ArrayObject $connections = null;

    /**
     * @var $layers ?\ArrayObject
     */
    public ?\ArrayObject $layers = null;

    /**
     * @var $rootLayer ?\Mwb\Grt\Model\Layer
     */
    public ?\Mwb\Grt\Model\Layer $rootLayer = null;

    /**
     * @var $selection ?\ArrayObject
     */
    public ?\ArrayObject $selection = null;

    /**
     * @var $height ?float
     */
    public ?float $height = null;

    /**
     * @var $width ?float
     */
    public ?float $width = null;

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

    /**
     * @var $updateBlocked ?int
     */
    public ?int $updateBlocked = null;

    /**
     * @var $closed ?int
     */
    public ?int $closed = null;

    /**
     * @var $options ?\ArrayObject diagram specific options
     */
    public ?\ArrayObject $options = null;
}

