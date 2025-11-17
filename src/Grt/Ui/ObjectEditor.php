<?php

namespace Mwb\Grt\Ui;

use \ArrayObject;
use \Mwb\Grt_Object;

class ObjectEditor extends \Mwb\Grt\TransientObject
{
    public ?\ArrayObject $customData = null;

    public ?\Mwb\Grt_Object $object = null;
}

