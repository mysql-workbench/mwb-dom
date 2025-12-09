<?php

namespace Mwb\Grt\Db\Oracle;

class Index extends \Mwb\Grt\Db\Index
{
    /**
     * @var $compression ?int
     */
    public ?int $compression = null;

    /**
     * @var $prefixLength ?int
     */
    public ?int $prefixLength = null;

    /**
     * @var $tablespace ?string
     */
    public ?string $tablespace = null;

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
     * @var $pctTreshold ?int
     */
    public ?int $pctTreshold = null;

    /**
     * @var $blevel ?int
     */
    public ?int $blevel = null;

    /**
     * @var $leafBlocks ?int
     */
    public ?int $leafBlocks = null;

    /**
     * @var $distinctKeys ?int
     */
    public ?int $distinctKeys = null;

    /**
     * @var $avgLeafBlocksPerKey ?int
     */
    public ?int $avgLeafBlocksPerKey = null;

    /**
     * @var $avgDataBlocksPerKey ?int
     */
    public ?int $avgDataBlocksPerKey = null;

    /**
     * @var $clusteringFactor ?int
     */
    public ?int $clusteringFactor = null;

    /**
     * @var $numRows ?int
     */
    public ?int $numRows = null;

    /**
     * @var $generated ?int
     */
    public ?int $generated = null;
}

