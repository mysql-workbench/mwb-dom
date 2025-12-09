<?php

namespace Mwb\Grt\Db;

class IndexColumn extends \Mwb\Grt_Object
{
    /**
     * @var $name ?string the object's name
     */
    public ?string $name = null;

    /**
     * @var $columnLength ?int
     */
    public ?int $columnLength = null;

    /**
     * @var $descend ?int
     */
    public ?int $descend = null;

    /**
     * @var $referencedColumn ?\Mwb\Grt\Db\Column
     */
    public ?\Mwb\Grt\Db\Column $referencedColumn = null;

    /**
     * @var $comment ?string
     */
    public ?string $comment = null;
}

