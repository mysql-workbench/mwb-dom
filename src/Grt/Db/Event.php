<?php

namespace Mwb\Grt\Db;

class Event extends \Mwb\Grt\Db\DatabaseDdlObject
{
    public ?string $at = null;

    public ?int $enabled = null;

    public ?string $interval = null;

    public ?string $intervalEnd = null;

    public ?string $intervalStart = null;

    public ?string $intervalUnit = null;

    public ?int $preserved = null;

    public ?int $useInterval = null;
}

