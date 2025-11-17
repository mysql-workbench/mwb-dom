<?php

namespace Mwb\Grt\App;

use \Mwb\Grt\App\PaperType;

class PageSettings extends \Mwb\Grt_Object
{
    public ?float $marginBottom = null;

    public ?float $marginLeft = null;

    public ?float $marginRight = null;

    public ?float $marginTop = null;

    public ?string $orientation = null;

    public ?\Mwb\Grt\App\PaperType $paperType = null;

    public ?float $scale = null;
}

