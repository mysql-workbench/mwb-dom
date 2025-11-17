<?php

namespace Mwb\Grt\Workbench\Logical;

class Connection extends \Mwb\Grt\Model\Connection
{
    public ?string $comment = null;

    public ?string $endCaption = null;

    public ?float $endCaptionXOffs = null;

    public ?float $endCaptionYOffs = null;

    public ?int $endMany = null;

    public ?string $startCaption = null;

    public ?float $startCaptionXOffs = null;

    public ?float $startCaptionYOffs = null;

    public ?int $startMany = null;
}

