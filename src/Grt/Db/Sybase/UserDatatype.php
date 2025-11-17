<?php

namespace Mwb\Grt\Db\Sybase;

class UserDatatype extends \Mwb\Grt\Db\UserDatatype
{
    public ?int $characterMaximumLength = null;

    public ?int $isNullable = null;

    public ?int $numericPrecision = null;

    public ?int $numericScale = null;
}

