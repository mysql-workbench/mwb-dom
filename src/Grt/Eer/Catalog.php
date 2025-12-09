<?php

namespace Mwb\Grt\Eer;

class Catalog extends \Mwb\Grt\NamedObject
{
    /**
     * @var $schemata ?\ArrayObject
     */
    public ?\ArrayObject $schemata = null;

    /**
     * @var $datatypes ?\ArrayObject
     */
    public ?\ArrayObject $datatypes = null;

    /**
     * @var $userDatatypes ?\ArrayObject
     */
    public ?\ArrayObject $userDatatypes = null;

    /**
     * @var $customData ?\ArrayObject a generic dictionary to hold additional
     * information used by e.g. plugins
     */
    public ?\ArrayObject $customData = null;
}

