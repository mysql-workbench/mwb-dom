<?php

namespace Mwb\Grt\Db\Oracle;

/**
 * an Oracle database routine object
 */
class Routine extends \Mwb\Grt\Db\Routine
{
    /**
     * @var $aggregate ?int
     */
    public ?int $aggregate = null;

    /**
     * @var $pipelined ?int
     */
    public ?int $pipelined = null;

    /**
     * @var $implTypeOwner ?string
     */
    public ?string $implTypeOwner = null;

    /**
     * @var $implTypeName ?string
     */
    public ?string $implTypeName = null;

    /**
     * @var $parallel ?int
     */
    public ?int $parallel = null;
}

