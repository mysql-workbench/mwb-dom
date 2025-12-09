<?php

namespace Mwb\Grt\Db\Sybase;

class Index extends \Mwb\Grt\Db\Index
{
    /**
     * @var $ignoreDuplicateRows ?int
     */
    public ?int $ignoreDuplicateRows = null;

    /**
     * @var $clustered ?int
     */
    public ?int $clustered = null;

    /**
     * @var $hasFilter ?int whether there is a filter associated to the index
     */
    public ?int $hasFilter = null;

    /**
     * @var $filterDefinition ?string the definition of the filter associated to the
     * index (expression for the subset of rows included in the filtered index)
     */
    public ?string $filterDefinition = null;
}

