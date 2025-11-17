<?php

namespace Mwb\Grt;

use \ArrayObject;

class LogEntry extends \Mwb\Grt_Object
{
    public ?\ArrayObject $customData = null;

    public ?int $entryType = null;
}

