<?php

namespace Mwb\Grt\App;

/**
 * information about the document
 */
class DocumentInfo extends \Mwb\Grt_Object
{
    /**
     * @var $caption ?string Caption of the document
     */
    public ?string $caption = null;

    /**
     * @var $version ?string Version of the document
     */
    public ?string $version = null;

    /**
     * @var $author ?string Author of the document
     */
    public ?string $author = null;

    /**
     * @var $project ?string Name of the project
     */
    public ?string $project = null;

    /**
     * @var $dateCreated ?string Date of creation of the document
     */
    public ?string $dateCreated = null;

    /**
     * @var $dateChanged ?string Date of last modification of the document
     */
    public ?string $dateChanged = null;

    /**
     * @var $description ?string Description/comments for the document
     */
    public ?string $description = null;
}

