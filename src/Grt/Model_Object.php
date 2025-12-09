<?php

namespace Mwb\Grt;

/**
 * a model object
 */
class Model_Object extends \Mwb\Grt_Object
{
    /**
     * @var $owner ?\Mwb\Grt\Model\Diagram
     */
    public ?\Mwb\Grt_Object $owner = null;

    /**
     * @var $visible ?int
     */
    public ?int $visible = null;
}

