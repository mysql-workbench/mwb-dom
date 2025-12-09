<?php

namespace Mwb\Grt\Workbench\Logical;

/**
 * a model connection
 */
class Connection extends \Mwb\Grt\Model\Connection
{
    /**
     * @var $startFigure ?\Mwb\Grt\Model\Figure the source figure
     */
    public ?\Mwb\Grt\Model\Figure $startFigure = null;

    /**
     * @var $endFigure ?\Mwb\Grt\Model\Figure the target figure
     */
    public ?\Mwb\Grt\Model\Figure $endFigure = null;

    /**
     * @var $startMany ?int
     */
    public ?int $startMany = null;

    /**
     * @var $endMany ?int
     */
    public ?int $endMany = null;

    /**
     * @var $startCaption ?string caption at the start of of the relationship
     */
    public ?string $startCaption = null;

    /**
     * @var $startCaptionXOffs ?float X offset of the start caption
     */
    public ?float $startCaptionXOffs = null;

    /**
     * @var $startCaptionYOffs ?float Y offset of the start caption
     */
    public ?float $startCaptionYOffs = null;

    /**
     * @var $endCaption ?string caption at the end of of the relationship
     */
    public ?string $endCaption = null;

    /**
     * @var $endCaptionXOffs ?float X offset of the end caption
     */
    public ?float $endCaptionXOffs = null;

    /**
     * @var $endCaptionYOffs ?float Y offset of the end caption
     */
    public ?float $endCaptionYOffs = null;

    /**
     * @var $comment ?string a comment about the relationship
     */
    public ?string $comment = null;
}

