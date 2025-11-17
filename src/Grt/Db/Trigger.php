<?php

namespace Mwb\Grt\Db;

class Trigger extends \Mwb\Grt\Db\DatabaseDdlObject
{
    public ?int $enabled = null;

    public ?string $event = null;

    public ?string $ordering = null;

    public ?string $otherTrigger = null;

    public ?string $timing = null;
}

