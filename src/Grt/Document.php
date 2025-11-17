<?php

namespace Mwb\Grt;

//use \Mwb\Grt\Workbench\Document as WorkbenchDocument;
use \Mwb\Grt\Version;

class Document
{
    public $format = '';

    public ?\Mwb\Grt\Version $version = null;

    /**
     * @var ?\Mwb\Grt\Workbench\Document $documentElement
     */
    public ?\Mwb\Grt\Workbench\Document $documentElement = null;

    public array $notes = [
        1 => 'My note',
    ];

    public array $db = [
        'data.db',
    ];
}

