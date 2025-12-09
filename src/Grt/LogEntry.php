<?php

namespace Mwb\Grt;

/**
 * GRT Log Entry
 *
 * an individual object log entry
 */
class LogEntry extends \Mwb\Grt_Object
{
    /**
     * @var string $name the log message
     */
    public ?string $name = null;

    /**
     * @var int $entryType type of the log entry, 0 for a normal message, 1 for a
     * warning and 2 for an error
     */
    public ?int $entryType = null;

    /**
     * @var \ArrayObject $customData
     */
    public ?\ArrayObject $customData = null;
}

