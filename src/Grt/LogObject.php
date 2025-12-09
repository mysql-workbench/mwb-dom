<?php

namespace Mwb\Grt;

/**
 * Log Object
 *
 * an object log
 */
class LogObject extends \Mwb\Grt_Object
{
    /**
     * @var \Mwb\Grt_Object $logObject a link to the object
     */
    public ?\Mwb\Grt_Object $logObject = null;

    /**
     * @var \Mwb\Grt_Object $refObject an optional link to a referenced object
     */
    public ?\Mwb\Grt_Object $refObject = null;

    /**
     * @var \ArrayObject<\Mwb\Grt\LogEntry> $entries the generated log messages
     */
    public ?\ArrayObject $entries = null;
}

