<?php

namespace Mwb\Grt\Workbench\Physical;

/**
 * a model connection
 */
class Connection extends \Mwb\Grt\Model\Connection
{
    /**
     * @var $caption ?string center caption
     */
    public ?string $caption = null;

    /**
     * @var $captionXOffs ?float X offset of the caption
     */
    public ?float $captionXOffs = null;

    /**
     * @var $captionYOffs ?float Y offset of the caption
     */
    public ?float $captionYOffs = null;

    /**
     * @var $extraCaption ?string additional center caption
     */
    public ?string $extraCaption = null;

    /**
     * @var $extraCaptionXOffs ?float X offset of the caption
     */
    public ?float $extraCaptionXOffs = null;

    /**
     * @var $extraCaptionYOffs ?float Y offset of the caption
     */
    public ?float $extraCaptionYOffs = null;

    /**
     * @var $startCaptionXOffs ?float X offset of the start caption
     */
    public ?float $startCaptionXOffs = null;

    /**
     * @var $startCaptionYOffs ?float Y offset of the start caption
     */
    public ?float $startCaptionYOffs = null;

    /**
     * @var $endCaptionXOffs ?float X offset of the end caption
     */
    public ?float $endCaptionXOffs = null;

    /**
     * @var $endCaptionYOffs ?float Y offset of the end caption
     */
    public ?float $endCaptionYOffs = null;

    /**
     * @var $middleSegmentOffset ?float offset of the middle segment of the line, if
     * applicable
     */
    public ?float $middleSegmentOffset = null;

    /**
     * @var $comment ?string a comment about the relationship
     */
    public ?string $comment = null;

    /**
     * @var $foreignKey ?\Mwb\Grt\Db\ForeignKey the foreign key this corresponds to
     */
    public ?\Mwb\Grt\Db\ForeignKey $foreignKey = null;
}

