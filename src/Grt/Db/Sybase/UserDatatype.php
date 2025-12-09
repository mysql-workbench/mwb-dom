<?php

namespace Mwb\Grt\Db\Sybase;

class UserDatatype extends \Mwb\Grt\Db\UserDatatype
{
    /**
     * @var $characterMaximumLength ?int maximum number of characters this datatype can
     * store
     */
    public ?int $characterMaximumLength = null;

    /**
     * @var $numericPrecision ?int maximum numbers of digits the datatype can store
     */
    public ?int $numericPrecision = null;

    /**
     * @var $numericScale ?int maximum numbers of digits right from the decimal point
     * the datatype can store
     */
    public ?int $numericScale = null;

    /**
     * @var $isNullable ?int whether NULL is a permitted value
     */
    public ?int $isNullable = null;
}

