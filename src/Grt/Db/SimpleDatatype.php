<?php

namespace Mwb\Grt\Db;

use \ArrayObject;
use \Mwb\Grt\Db\DatatypeGroup;

class SimpleDatatype extends \Mwb\Grt_Object
{
    public ?int $characterMaximumLength = null;

    public ?int $characterOctetLength = null;

    public ?int $dateTimePrecision = null;

    public ?\ArrayObject $flags = null;

    public ?\Mwb\Grt\Db\DatatypeGroup $group = null;

    public ?int $needsQuotes = null;

    public ?int $numericPrecision = null;

    public ?int $numericPrecisionRadix = null;

    public ?int $numericScale = null;

    public ?int $parameterFormatType = null;

    public ?\ArrayObject $synonyms = null;

    public ?string $validity = null;
}

