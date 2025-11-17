<?php

namespace Mwb\Grt\Db\Mssql;

class UserDatatype extends \Mwb\Grt\Db\UserDatatype
{
    public ?int $characterMaximumLength = null;

    public ?int $isNullable = null;

    public ?int $numericPrecision = null;

    public ?int $numericScale = null;
}

