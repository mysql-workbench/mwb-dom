<?php

namespace Mwb\Grt\Db;

class StructuredDatatype extends \Mwb\Grt\Db\DatabaseObject
{
    /**
     * @var $parentType ?\Mwb\Grt\Db\StructuredDatatype
     */
    public ?\Mwb\Grt\Db\StructuredDatatype $parentType = null;

    /**
     * @var $distinctTypes ?\ArrayObject
     */
    public ?\ArrayObject $distinctTypes = null;
}

