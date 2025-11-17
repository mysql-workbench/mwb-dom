<?php

namespace Mwb\Grt\Db;

class Sequence extends \Mwb\Grt\Db\DatabaseObject
{
    public ?string $cacheSize = null;

    public ?int $cycleFlag = null;

    public ?string $incrementBy = null;

    public ?string $lastNumber = null;

    public ?string $maxValue = null;

    public ?string $minValue = null;

    public ?int $orderFlag = null;

    public ?string $startValue = null;
}

