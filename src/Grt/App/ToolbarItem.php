<?php

namespace Mwb\Grt\App;

class ToolbarItem extends \Mwb\Grt\App\CommandItem
{
    /**
     * @var $icon ?string
     */
    public ?string $icon = null;

    /**
     * @var $altIcon ?string
     */
    public ?string $altIcon = null;

    /**
     * @var $itemType ?string type of button (action, separator, toggle,
     * segmentedToggle, radio, label, dropdown)
     */
    public ?string $itemType = null;

    /**
     * @var $tooltip ?string
     */
    public ?string $tooltip = null;

    /**
     * @var $initialState ?int For (segmented) toggle only: is the item checked
     * initially?
     */
    public ?int $initialState = null;
}

