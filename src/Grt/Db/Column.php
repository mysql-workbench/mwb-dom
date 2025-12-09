<?php

namespace Mwb\Grt\Db;

class Column extends \Mwb\Grt\NamedObject
{
    /**
     * @var $simpleType ?\Mwb\Grt\Db\SimpleDatatype
     */
    public ?\Mwb\Grt\Db\SimpleDatatype $simpleType = null;

    /**
     * @var $userType ?\Mwb\Grt\Db\UserDatatype
     */
    public ?\Mwb\Grt\Db\UserDatatype $userType = null;

    /**
     * @var $structuredType ?\Mwb\Grt\Db\StructuredDatatype
     */
    public ?\Mwb\Grt\Db\StructuredDatatype $structuredType = null;

    /**
     * @var $precision ?int for numeric types this represents the total number of
     * digits that are stored including digits right from the decimal point
     */
    public ?int $precision = null;

    /**
     * @var $scale ?int the number of digits right to the decimal point
     */
    public ?int $scale = null;

    /**
     * @var $length ?int The total length of the column. For string types this referes
     * to the number of characters that can be stored.
     */
    public ?int $length = null;

    /**
     * @var $datatypeExplicitParams ?string For ENUM, SET and similar datatypes the
     * parametes can be defined explictly. Note that brackets need to be included. This
     * will overwrite the precision, scale and length setting
     */
    public ?string $datatypeExplicitParams = null;

    /**
     * @var $formattedType ?string
     */
    public ?string $formattedType = null;

    /**
     * @var $formattedRawType ?string
     */
    public ?string $formattedRawType = null;

    /**
     * @var $isNotNull ?int
     */
    public ?int $isNotNull = null;

    /**
     * @var $flags ?\ArrayObject additional flags like UNSIGNED, ZEROFILL, BINARY
     */
    public ?\ArrayObject $flags = null;

    /**
     * @var $checks ?\ArrayObject
     */
    public ?\ArrayObject $checks = null;

    /**
     * @var $defaultValue ?string
     */
    public ?string $defaultValue = null;

    /**
     * @var $defaultValueIsNull ?int
     */
    public ?int $defaultValueIsNull = null;

    /**
     * @var $characterSetName ?string
     */
    public ?string $characterSetName = null;

    /**
     * @var $collationName ?string
     */
    public ?string $collationName = null;
}

