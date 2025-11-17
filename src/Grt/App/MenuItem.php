<?php

namespace Mwb\Grt\App;

use \ArrayObject;

class MenuItem extends \Mwb\Grt\App\CommandItem
{
    public ?string $caption = null;

    public ?string $itemType = null;

    public ?string $shortcut = null;

    public ?\ArrayObject $subItems = null;
}

