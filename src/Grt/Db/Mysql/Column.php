<?php

namespace Mwb\Grt\Db\Mysql;

class Column extends \Mwb\Grt\Db\Column
{
    /**
     * @var $autoIncrement ?int
     */
    public ?int $autoIncrement = null;

    /**
     * @var $generated ?int 0 or 1, 1 if generated column
     */
    public ?int $generated = null;

    /**
     * @var $expression ?string The full expression for a generated column as text
     */
    public ?string $expression = null;

    /**
     * @var $generatedStorage ?string VIRTUAL or STORED, for generated columns only
     */
    public ?string $generatedStorage = null;
}

