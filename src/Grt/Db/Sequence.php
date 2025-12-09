<?php

namespace Mwb\Grt\Db;

/**
 * a database sequence object
 */
class Sequence extends \Mwb\Grt\Db\DatabaseObject
{
    /**
     * @var $minValue ?string Minimum value of sequence
     */
    public ?string $minValue = null;

    /**
     * @var $maxValue ?string Maximum value of sequence
     */
    public ?string $maxValue = null;

    /**
     * @var $startValue ?string The value that starts the sequence
     */
    public ?string $startValue = null;

    /**
     * @var $incrementBy ?string Value by which sequence is increased
     */
    public ?string $incrementBy = null;

    /**
     * @var $cycleFlag ?int Does sequence begin again with minimum value once maximum
     * value has been reached?
     */
    public ?int $cycleFlag = null;

    /**
     * @var $orderFlag ?int Are the sequence values granted in the order of the
     * request?
     */
    public ?int $orderFlag = null;

    /**
     * @var $cacheSize ?string Number of sequence values that are loaded into cache
     * simultaneously
     */
    public ?string $cacheSize = null;

    /**
     * @var $lastNumber ?string Last sequence value that was saved
     */
    public ?string $lastNumber = null;
}

