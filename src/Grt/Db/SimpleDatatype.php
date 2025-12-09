<?php

namespace Mwb\Grt\Db;

class SimpleDatatype extends \Mwb\Grt_Object
{
    /**
     * @var $group ?\Mwb\Grt\Db\DatatypeGroup the datatype group this datatype belongs
     * to
     */
    public ?\Mwb\Grt\Db\DatatypeGroup $group = null;

    /**
     * @var $characterMaximumLength ?int maximum number of characters this datatype can
     * store
     */
    public ?int $characterMaximumLength = null;

    /**
     * @var $characterOctetLength ?int maximum number of 8 bit characters this datatype
     * can store
     */
    public ?int $characterOctetLength = null;

    /**
     * @var $parameterFormatType ?int 0 none, 1 (n), 2 [(n)], 3 (m,n), 4 (m[,n]), 5
     * [(m,n)], 6 [(m[, n])], 10 ('a','b','c')
     */
    public ?int $parameterFormatType = null;

    /**
     * @var $numericPrecision ?int maximum numbers of digits the datatype can store
     */
    public ?int $numericPrecision = null;

    /**
     * @var $numericPrecisionRadix ?int
     */
    public ?int $numericPrecisionRadix = null;

    /**
     * @var $numericScale ?int maximum numbers of digits right from the decimal point
     * the datatype can store
     */
    public ?int $numericScale = null;

    /**
     * @var $dateTimePrecision ?int the datetime precision the datatype can store
     */
    public ?int $dateTimePrecision = null;

    /**
     * @var $needsQuotes ?int whether values require quotes around them
     */
    public ?int $needsQuotes = null;

    /**
     * @var $validity ?string information about validity of this type. Allowed:
     * comparison operator followed by version number
     */
    public ?string $validity = null;

    /**
     * @var $synonyms ?\ArrayObject the list of names that can be used as synonym for
     * the datatype
     */
    public ?\ArrayObject $synonyms = null;

    /**
     * @var $flags ?\ArrayObject additional flags like UNSIGNED, ZEROFILL, BINARY
     */
    public ?\ArrayObject $flags = null;
}

