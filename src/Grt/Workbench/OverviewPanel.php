<?php

namespace Mwb\Grt\Workbench;

use \ArrayObject;

class OverviewPanel extends \Mwb\Grt_Object
{
    public ?string $caption = null;

    public ?int $expanded = null;

    public ?int $expandedHeight = null;

    public ?int $hasTabSelection = null;

    public ?string $implModule = null;

    public ?string $itemActivationFunction = null;

    public ?string $itemCountFunction = null;

    public ?int $itemDisplayMode = null;

    public ?string $itemInfoFunction = null;

    public ?string $nodeId = null;

    public ?\ArrayObject $selectedItems = null;

    public ?string $tabActivationFunction = null;

    public ?string $tabCountFunction = null;

    public ?string $tabInfoFunction = null;
}

