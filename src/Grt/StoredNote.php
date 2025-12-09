<?php

namespace Mwb\Grt;

/**
 * a note
 */
class StoredNote extends \Mwb\Grt\NamedObject
{
    public ?string $filename = null;

    public ?string $createDate = null;

    public ?string $lastChangeDate = null;
}

