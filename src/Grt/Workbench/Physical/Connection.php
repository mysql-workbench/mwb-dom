<?php

namespace Mwb\Grt\Workbench\Physical;

use \Mwb\Grt\Db\ForeignKey;

class Connection extends \Mwb\Grt\Model\Connection
{
    public ?string $caption = null;

    public ?float $captionXOffs = null;

    public ?float $captionYOffs = null;

    public ?string $comment = null;

    public ?float $endCaptionXOffs = null;

    public ?float $endCaptionYOffs = null;

    public ?string $extraCaption = null;

    public ?float $extraCaptionXOffs = null;

    public ?float $extraCaptionYOffs = null;

    public ?\Mwb\Grt\Db\ForeignKey $foreignKey = null;

    public ?float $middleSegmentOffset = null;

    public ?float $startCaptionXOffs = null;

    public ?float $startCaptionYOffs = null;
}

