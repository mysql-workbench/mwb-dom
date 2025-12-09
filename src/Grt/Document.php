<?php

namespace Mwb\Grt;

/**
 * GRT root element
 */
class Document
{
    /**
     * @var string $format
     */
    public ?string $format = null;

    /**
     * @var \Mwb\Grt\Version $version
     */
    public ?\Mwb\Grt\Version $version = null;

    /**
     * @var \Mwb\Grt\Workbench\Document $documentElement
     */
    public ?\Mwb\Grt\Workbench\Document $documentElement = null;

    /**
     * @var \ArrayObject $notes
     */
    public ?\ArrayObject $notes = null;

    /**
     * @var \ArrayObject $db
     */
    public ?\ArrayObject $db = null;
}

