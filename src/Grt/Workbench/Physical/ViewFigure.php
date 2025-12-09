<?php

namespace Mwb\Grt\Workbench\Physical;

/**
 * a model figure representing a view
 */
class ViewFigure extends \Mwb\Grt\Model\Figure
{
    /**
     * @var $view ?\Mwb\Grt\Db\View the view this figure represents
     */
    public ?\Mwb\Grt\Db\View $view = null;
}

