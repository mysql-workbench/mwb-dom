<?php

namespace Mwb\Grt\Eer;

use \ArrayObject;

class Catalog extends \Mwb\Grt\NamedObject
{
    public ?\ArrayObject $customData = null;

    public ?\ArrayObject $datatypes = null;

    public ?\ArrayObject $schemata = null;

    public ?\ArrayObject $userDatatypes = null;
}

