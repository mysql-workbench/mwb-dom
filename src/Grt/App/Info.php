<?php

namespace Mwb\Grt\App;

use \Mwb\Grt\Version;

class Info extends \Mwb\Grt_Object
{
    public ?string $caption = null;

    public ?string $copyright = null;

    public ?string $description = null;

    public ?string $edition = null;

    public ?string $license = null;

    public ?\Mwb\Grt\Version $version = null;
}

