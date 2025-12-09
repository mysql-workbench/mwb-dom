<?php

namespace Mwb\Grt\Db;

class Index extends \Mwb\Grt\Db\DatabaseObject
{
    /**
     * @var $name string
     */
    public ?string $name = null;

    /**
     * @var $isPrimary ?int
     */
    public ?int $isPrimary = null;

    /**
     * @var $deferability ?int
     */
    public ?int $deferability = null;

    /**
     * @var $unique ?int
     */
    public ?int $unique = null;

    /**
     * @var $indexType ?string one of INDEX, PRIMARY, UNIQUE, FULLTEXT and SPATIAL
     */
    public ?string $indexType = null;

    /**
     * @var $columns ?\ArrayObject
     */
    public ?\ArrayObject $columns = null;

    /**
     * @var $comment ?string
     */
    public ?string $comment = null;
}

