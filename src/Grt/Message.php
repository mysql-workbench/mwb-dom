<?php

namespace Mwb\Grt;

/**
 * GRT Message
 *
 * a dictionary containing a GRT message
 */
class Message extends \Mwb\Grt_Object
{
    /**
     * @var string $name the message's title
     */
    public ?string $name = null;

    /**
     * @var int $msgType The type of the message, 0 stands for a normal message, 1 for
     * a warning and 2 for an error
     */
    public ?int $msgType = null;

    /**
     * @var string $msg The message string
     */
    public ?string $msg = null;

    /**
     * @var ArrayObject<string> $details A list of detail information strings belonging
     * to the message
     */
    public ?\ArrayObject $details = null;
}

