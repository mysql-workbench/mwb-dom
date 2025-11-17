<?php

namespace Mwb\Grt\Db;

use \ArrayObject;
use \Mwb\Grt\Db\SimpleDatatype;
use \Mwb\Grt\Db\StructuredDatatype;
use \Mwb\Grt\Db\UserDatatype;

class Column extends \Mwb\Grt\NamedObject
{
    public ?string $characterSetName = null;

    public ?\ArrayObject $checks = null;

    public ?string $collationName = null;

    public ?string $datatypeExplicitParams = null;

    public ?string $defaultValue = null;

    public ?int $defaultValueIsNull = null;

    public ?\ArrayObject $flags = null;

    public ?int $isNotNull = null;

    public ?int $length = null;

    public ?int $precision = null;

    public ?int $scale = null;

    public ?\Mwb\Grt\Db\SimpleDatatype $simpleType = null;

    public ?\Mwb\Grt\Db\StructuredDatatype $structuredType = null;

    public ?\Mwb\Grt\Db\UserDatatype $userType = null;
}

