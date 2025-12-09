<?php

namespace Mwb\Grt\Model;

/**
 * a connection between figures
 */
class Connection extends \Mwb\Grt\Model_Object
{
    /**
     * @var $drawSplit ?int set to 1 if the connection line should be drawn split
     */
    public ?int $drawSplit = null;

    /**
     * @var $startFigure ?\Mwb\Grt\Model\Figure the source figure
     */
    public ?\Mwb\Grt\Model\Figure $startFigure = null;

    /**
     * @var $endFigure ?\Mwb\Grt\Model\Figure the target figure
     */
    public ?\Mwb\Grt\Model\Figure $endFigure = null;
}

