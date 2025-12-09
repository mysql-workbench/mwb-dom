<?php

namespace Mwb\Grt\Workbench;

class OverviewPanel extends \Mwb\Grt_Object
{
    /**
     * @var $nodeId ?string the identifier of panel in the GUI
     */
    public ?string $nodeId = null;

    /**
     * @var $expanded ?int specifies if the panel is currently expanded
     */
    public ?int $expanded = null;

    /**
     * @var $expandedHeight ?int the panel's height when it is expanded
     */
    public ?int $expandedHeight = null;

    /**
     * @var $selectedItems ?\ArrayObject specifies the indexes of the currently
     * selected items on the panel
     */
    public ?\ArrayObject $selectedItems = null;

    /**
     * @var $hasTabSelection ?int specifies if the panel has attached tabs
     */
    public ?int $hasTabSelection = null;

    /**
     * @var $itemDisplayMode ?int defines how the items on the panel are displayed.
     * Setting it to 1 will cause a large icon display, 2 a small icon display and 3 a
     * list
     */
    public ?int $itemDisplayMode = null;

    /**
     * @var $caption ?string the caption displayed on the panel's header
     */
    public ?string $caption = null;

    /**
     * @var $implModule ?string GRT module implementing the item functionality
     */
    public ?string $implModule = null;

    /**
     * @var $itemCountFunction ?string the function that returns the number of items
     */
    public ?string $itemCountFunction = null;

    /**
     * @var $itemInfoFunction ?string the function that returns the item information
     */
    public ?string $itemInfoFunction = null;

    /**
     * @var $itemActivationFunction ?string the function that is called when the item
     * is activated
     */
    public ?string $itemActivationFunction = null;

    /**
     * @var $tabCountFunction ?string the function that returns the number of tabs
     */
    public ?string $tabCountFunction = null;

    /**
     * @var $tabInfoFunction ?string the function that returns the tab information
     */
    public ?string $tabInfoFunction = null;

    /**
     * @var $tabActivationFunction ?string the function that is called when the tab is
     * activated
     */
    public ?string $tabActivationFunction = null;
}

