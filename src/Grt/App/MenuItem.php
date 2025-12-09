<?php

namespace Mwb\Grt\App;

class MenuItem extends \Mwb\Grt\App\CommandItem
{
    /**
     * @var $caption ?string
     */
    public ?string $caption = null;

    /**
     * @var $itemType ?string type of item (action, separator, cascade, check, radio)
     */
    public ?string $itemType = null;

    /**
     * @var $subItems ?\ArrayObject
     */
    public ?\ArrayObject $subItems = null;

    /**
     * @var $shortcut ?string optional shortcut (eg: control+s)
     */
    public ?string $shortcut = null;
}

