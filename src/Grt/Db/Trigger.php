<?php

namespace Mwb\Grt\Db;

class Trigger extends \Mwb\Grt\Db\DatabaseDdlObject
{
    /**
     * @var $name string
     */
    public ?string $name = null;

    /**
     * @var $event ?string what fires the trigger (INSERT, UPDATE or DELETE)
     */
    public ?string $event = null;

    /**
     * @var $ordering ?string the order in which triggers of the same event and timing
     * are executed (FOLLOWS or PRECEDES)
     */
    public ?string $ordering = null;

    /**
     * @var $otherTrigger ?string the name of the trigger to which order is relative
     */
    public ?string $otherTrigger = null;

    /**
     * @var $timing ?string when the trigger fires (AFTER or BEFORE)
     */
    public ?string $timing = null;

    /**
     * @var $enabled ?int
     */
    public ?int $enabled = null;
}

