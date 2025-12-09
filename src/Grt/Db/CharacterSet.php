<?php

namespace Mwb\Grt\Db;

class CharacterSet extends \Mwb\Grt_Object
{
    /**
     * @var $description ?string
     */
    public ?string $description = null;

    /**
     * @var $defaultCollation ?string
     */
    public ?string $defaultCollation = null;

    /**
     * @var $collations ?\ArrayObject
     */
    public ?\ArrayObject $collations = null;
}

