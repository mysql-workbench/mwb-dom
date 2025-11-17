<?php

namespace Mwb\Grt\Db;

class LogFileGroup extends \Mwb\Grt\Db\DatabaseObject
{
    public ?int $initialSize = null;

    public ?int $undoBufferSize = null;

    public ?string $undoFile = null;
}

