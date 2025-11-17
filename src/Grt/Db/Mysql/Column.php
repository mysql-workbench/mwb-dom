<?php

namespace Mwb\Grt\Db\Mysql;

class Column extends \Mwb\Grt\Db\Column
{
    public ?int $autoIncrement = null;

    public ?string $expression = null;

    public ?int $generated = null;

    public ?string $generatedStorage = null;
}

