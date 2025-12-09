<?php

namespace Mwb\Grt\App;

class PageSettings extends \Mwb\Grt_Object
{
    /**
     * @var $paperType ?\Mwb\Grt\App\PaperType type of paper size (A4, letter etc)
     */
    public ?\Mwb\Grt\App\PaperType $paperType = null;

    /**
     * @var $orientation ?string landscape or portrait
     */
    public ?string $orientation = null;

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

    /**
     * @var $scale ?float
     */
    public ?float $scale = null;
}

