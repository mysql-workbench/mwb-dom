<?php

namespace Mwb\Grt\Ui;

use \ArrayObject;
use \Mwb\Grt\Model\Model;

class ModelPanel extends \Mwb\Grt\TransientObject
{
    public ?\ArrayObject $customData = null;

    public ?\Mwb\Grt\Model\Model $model = null;
}

