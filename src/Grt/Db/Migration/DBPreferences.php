<?php

namespace Mwb\Grt\Db\Migration;

class DBPreferences extends \Mwb\Grt_Object
{
    /**
     * @var $sourceRdbms ?\Mwb\Grt\Db\Mgmt\Rdbms
     */
    public ?\Mwb\Grt\Db\Mgmt\Rdbms $sourceRdbms = null;

    /**
     * @var $defaultValueMapping ?\ArrayObject
     */
    public ?\ArrayObject $defaultValueMapping = null;

    /**
     * @var $datatypeMapping ?\ArrayObject
     */
    public ?\ArrayObject $datatypeMapping = null;

    /**
     * @var $characterSetMapping ?\ArrayObject
     */
    public ?\ArrayObject $characterSetMapping = null;

    /**
     * @var $options ?\ArrayObject
     */
    public ?\ArrayObject $options = null;
}

