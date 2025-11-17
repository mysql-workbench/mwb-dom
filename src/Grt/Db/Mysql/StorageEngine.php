<?php

namespace Mwb\Grt\Db\Mysql;

use \ArrayObject;

class StorageEngine extends \Mwb\Grt\NamedObject
{
    public ?string $caption = null;

    public ?string $description = null;

    public ?\ArrayObject $options = null;

    public ?int $supportsForeignKeys = null;
}

