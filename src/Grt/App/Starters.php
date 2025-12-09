<?php

namespace Mwb\Grt\App;

/**
 * Stores all defined home screen starters.
 */
class Starters extends \Mwb\Grt_Object
{
    /**
     * @var $predefined ?\ArrayObject
     */
    public ?\ArrayObject $predefined = null;

    /**
     * @var $custom ?\ArrayObject
     */
    public ?\ArrayObject $custom = null;

    /**
     * @var $displayList ?\ArrayObject
     */
    public ?\ArrayObject $displayList = null;
}

