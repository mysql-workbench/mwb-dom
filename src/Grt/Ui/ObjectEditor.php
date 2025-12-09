<?php

namespace Mwb\Grt\Ui;

class ObjectEditor extends \Mwb\Grt\TransientObject
{
    /**
     * @var $object ?\Mwb\Grt_Object
     */
    public ?\Mwb\Grt_Object $object = null;

    /**
     * @var $dockingPoint ?\Mwb\Grt\Mforms\ObjectReference
     */
    public ?\Mwb\Grt\Mforms\ObjectReference $dockingPoint = null;

    /**
     * @var $customData ?\ArrayObject
     */
    public ?\ArrayObject $customData = null;
}

