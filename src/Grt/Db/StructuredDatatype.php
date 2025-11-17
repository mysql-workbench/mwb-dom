<?php

namespace Mwb\Grt\Db;

use \ArrayObject;

class StructuredDatatype extends \Mwb\Grt\Db\DatabaseObject
{
    public ?\ArrayObject $distinctTypes = null;

    public ?\Mwb\Grt\Db\StructuredDatatype $parentType = null;
}

