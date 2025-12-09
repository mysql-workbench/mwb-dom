<?php

namespace Mwb\Grt\Db;

class Event extends \Mwb\Grt\Db\DatabaseDdlObject
{
    /**
     * @var $name string
     */
    public ?string $name = null;

    /**
     * @var $definer ?string a full user name ('user'@'host') or CURRENT_USER
     */
    public ?string $definer = null;

    /**
     * @var $useInterval ?int 1 if to use the interval* members, otherwise for at
     */
    public ?int $useInterval = null;

    /**
     * @var $at ?string the expression to define an execution timestamp, mutually
     * exclusive with the interval* members
     */
    public ?string $at = null;

    /**
     * @var $interval ?string the expression to define an interval, mutually exclusive
     * with the at member
     */
    public ?string $interval = null;

    /**
     * @var $intervalUnit ?string one of the interval units, except microseconds, e.g.
     * SECOND, HOUR etc.
     */
    public ?string $intervalUnit = null;

    /**
     * @var $intervalStart ?string optional expression for a start timestamp
     */
    public ?string $intervalStart = null;

    /**
     * @var $intervalEnd ?string optional expression for an end timestamp
     */
    public ?string $intervalEnd = null;

    /**
     * @var $preserved ?int 0 if the event is automatically dropped after last
     * occurrence
     */
    public ?int $preserved = null;

    /**
     * @var $enabled ?int
     */
    public ?int $enabled = null;

    /**
     * @var $comment ?string
     */
    public ?string $comment = null;

    /**
     * @var $sqlBody ?string the sql code to execute on each invocation of the event
     */
    public ?string $sqlBody = null;
}

