<?php

namespace Mwb\Grt\Db\Oracle;

/**
 * an Oracle database view object
 */
class View extends \Mwb\Grt\Db\View
{
    /**
     * @var $typedText ?string
     */
    public ?string $typedText = null;

    /**
     * @var $oidText ?string
     */
    public ?string $oidText = null;

    /**
     * @var $viewTypeOwner ?string
     */
    public ?string $viewTypeOwner = null;

    /**
     * @var $viewType ?string
     */
    public ?string $viewType = null;

    /**
     * @var $superViewName ?string
     */
    public ?string $superViewName = null;
}

