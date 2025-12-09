<?php

namespace Mwb\Grt\Db\Oracle;

class Column extends \Mwb\Grt\Db\Column
{
    /**
     * @var $datatypeModifier ?string
     */
    public ?string $datatypeModifier = null;

    /**
     * @var $defaultLength ?int
     */
    public ?int $defaultLength = null;

    /**
     * @var $density ?float
     */
    public ?float $density = null;

    /**
     * @var $numberOfNulls ?int
     */
    public ?int $numberOfNulls = null;

    /**
     * @var $numberOfBuckets ?int
     */
    public ?int $numberOfBuckets = null;
}

