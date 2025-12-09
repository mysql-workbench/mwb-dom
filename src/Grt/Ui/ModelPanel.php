<?php

namespace Mwb\Grt\Ui;

class ModelPanel extends \Mwb\Grt\TransientObject
{
    /**
     * @var $model ?\Mwb\Grt\Model\Model
     */
    public ?\Mwb\Grt\Model\Model $model = null;

    /**
     * @var $commonSidebar ?\Mwb\Grt\Mforms\ObjectReference
     */
    public ?\Mwb\Grt\Mforms\ObjectReference $commonSidebar = null;

    /**
     * @var $customData ?\ArrayObject
     */
    public ?\ArrayObject $customData = null;
}

