<?php

namespace Mwb\Grt\Db\Mysql;

class Index extends \Mwb\Grt\Db\Index
{
    /**
     * @var $indexKind ?string one of BTREE, RTREE and HASH
     */
    public ?string $indexKind = null;

    /**
     * @var $keyBlockSize ?int
     */
    public ?int $keyBlockSize = null;

    /**
     * @var $withParser ?string
     */
    public ?string $withParser = null;

    /**
     * @var $algorithm ?string one of DEFAULT, INPLACE and COPY
     */
    public ?string $algorithm = null;

    /**
     * @var $lockOption ?string one of DEFAULT, NONE, SHARED and EXCLUSIVE
     */
    public ?string $lockOption = null;

    /**
     * @var $columns \Mwb\Grt\Db\Mysql\IndexColumn[]
     */
    public ?\ArrayObject $columns = null;
}

