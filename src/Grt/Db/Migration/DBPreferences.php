<?php

namespace Mwb\Grt\Db\Migration;

use \ArrayObject;
use \Mwb\Grt\Db\Mgmt\Rdbms;

class DBPreferences extends \Mwb\Grt_Object
{
    public ?\ArrayObject $characterSetMapping = null;

    public ?\ArrayObject $datatypeMapping = null;

    public ?\ArrayObject $defaultValueMapping = null;

    public ?\ArrayObject $options = null;

    public ?\Mwb\Grt\Db\Mgmt\Rdbms $sourceRdbms = null;
}

