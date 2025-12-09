<?php

namespace Mwb\Grt\App;

class CommandItem extends \Mwb\Grt_Object
{
    /**
     * @var $context ?string application context where the item is valid (eg global,
     * model etc)
     */
    public ?string $context = null;

    /**
     * @var $platform ?string windows, linux, macosx
     */
    public ?string $platform = null;

    /**
     * @var $command ?string command name if builtin, or plugin name
     */
    public ?string $command = null;
}

