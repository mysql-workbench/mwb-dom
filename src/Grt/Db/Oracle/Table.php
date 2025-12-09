<?php

namespace Mwb\Grt\Db\Oracle;

/**
 * an Oracle database table object with all specific options and required for a
 * Oracle 8i/9i/10i database
 */
class Table extends \Mwb\Grt\Db\Table
{
    /**
     * @var $tablespaceName ?string
     */
    public ?string $tablespaceName = null;

    /**
     * @var $clusterName ?string
     */
    public ?string $clusterName = null;

    /**
     * @var $pctFreeBlockSpace ?int
     */
    public ?int $pctFreeBlockSpace = null;

    /**
     * @var $pctUsedBlockSpace ?int
     */
    public ?int $pctUsedBlockSpace = null;

    /**
     * @var $initialTrans ?int
     */
    public ?int $initialTrans = null;

    /**
     * @var $maxTrans ?int
     */
    public ?int $maxTrans = null;

    /**
     * @var $initialExtent ?int
     */
    public ?int $initialExtent = null;

    /**
     * @var $nextExtent ?int
     */
    public ?int $nextExtent = null;

    /**
     * @var $minExtents ?int
     */
    public ?int $minExtents = null;

    /**
     * @var $maxExtents ?int
     */
    public ?int $maxExtents = null;

    /**
     * @var $pctIncrease ?int
     */
    public ?int $pctIncrease = null;

    /**
     * @var $numRows ?int
     */
    public ?int $numRows = null;

    /**
     * @var $blocks ?int
     */
    public ?int $blocks = null;

    /**
     * @var $emptyBlocks ?int
     */
    public ?int $emptyBlocks = null;

    /**
     * @var $avgSpace ?int
     */
    public ?int $avgSpace = null;

    /**
     * @var $avgRowLen ?int
     */
    public ?int $avgRowLen = null;

    /**
     * @var $isNestedTable ?int
     */
    public ?int $isNestedTable = null;
}

