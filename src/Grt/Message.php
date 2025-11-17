<?php

namespace Mwb\Grt;

use \ArrayObject;

class Message extends \Mwb\Grt_Object
{
    public ?\ArrayObject $details = null;

    public ?string $msg = null;

    public ?int $msgType = null;
}

