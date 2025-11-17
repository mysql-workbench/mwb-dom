<?php

namespace Mwb\Grt;

use \ArrayObject;

class LogObject extends \Mwb\Grt_Object
{
    public ?\ArrayObject $entries = null;

    public ?\Mwb\Grt_Object $logObject = null;

    public ?\Mwb\Grt_Object $refObject = null;
}

