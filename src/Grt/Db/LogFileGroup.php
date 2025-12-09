<?php

namespace Mwb\Grt\Db;

class LogFileGroup extends \Mwb\Grt\Db\DatabaseObject
{
    /**
     * @var $undoFile ?string
     */
    public ?string $undoFile = null;

    /**
     * @var $initialSize ?int
     */
    public ?int $initialSize = null;

    /**
     * @var $undoBufferSize ?int
     */
    public ?int $undoBufferSize = null;
}

