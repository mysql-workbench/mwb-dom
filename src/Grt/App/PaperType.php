<?php

namespace Mwb\Grt\App;

class PaperType extends \Mwb\Grt_Object
{
    /**
     * @var $caption ?string
     */
    public ?string $caption = null;

    /**
     * @var $width ?float
     */
    public ?float $width = null;

    /**
     * @var $height ?float
     */
    public ?float $height = null;

    /**
     * @var $marginsSet ?int
     */
    public ?int $marginsSet = null;

    /**
     * @var $marginTop ?float
     */
    public ?float $marginTop = null;

    /**
     * @var $marginBottom ?float
     */
    public ?float $marginBottom = null;

    /**
     * @var $marginLeft ?float
     */
    public ?float $marginLeft = null;

    /**
     * @var $marginRight ?float
     */
    public ?float $marginRight = null;
}

