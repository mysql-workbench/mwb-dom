<?php

namespace Mwb\Grt\Db\Query;

use \Mwb\Grt\Db\Query\QueryEditor;
use \ArrayObject;
use \Mwb\Grt\Version;

class Editor extends \Mwb\Grt_Object
{
    public ?\Mwb\Grt\Db\Query\QueryEditor $activeQueryEditor = null;

    public ?\ArrayObject $customData = null;

    public ?\ArrayObject $queryEditors = null;

    public ?\Mwb\Grt\Version $serverVersion = null;
}

