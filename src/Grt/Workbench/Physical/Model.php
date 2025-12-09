<?php

namespace Mwb\Grt\Workbench\Physical;

/**
 * a physical model holding diagrams
 */
class Model extends \Mwb\Grt\Model\Model
{
    /**
     * @var $diagrams \Mwb\Grt\Workbench\Physical\Diagram[] the list of all available
     * diagrams
     */
    public ?\ArrayObject $diagrams = null;

    /**
     * @var $rdbms ?\Mwb\Grt\Db\Mgmt\Rdbms the rdbms used for the document
     */
    public ?\Mwb\Grt\Db\Mgmt\Rdbms $rdbms = null;

    /**
     * @var $currentConnection ?\Mwb\Grt\Db\Mgmt\Connection the connection used for
     * reverse engineering and synchronisation
     */
    public ?\Mwb\Grt\Db\Mgmt\Connection $currentConnection = null;

    /**
     * @var $connections ?\ArrayObject all connections that should be used for a full
     * synchronisation
     */
    public ?\ArrayObject $connections = null;

    /**
     * @var $catalog ?\Mwb\Grt\Db\Catalog
     */
    public ?\Mwb\Grt\Db\Catalog $catalog = null;

    /**
     * @var $figureNotation ?string
     */
    public ?string $figureNotation = null;

    /**
     * @var $connectionNotation ?string
     */
    public ?string $connectionNotation = null;

    /**
     * @var $scripts ?\ArrayObject a list of scripts that are stored with the model
     */
    public ?\ArrayObject $scripts = null;

    /**
     * @var $notes ?\ArrayObject a list of notes that are stored with the model
     */
    public ?\ArrayObject $notes = null;

    /**
     * @var $tags ?\ArrayObject
     */
    public ?\ArrayObject $tags = null;

    /**
     * @var $tagCategories ?\ArrayObject
     */
    public ?\ArrayObject $tagCategories = null;

    /**
     * @var $syncProfiles ?\ArrayObject
     */
    public ?\ArrayObject $syncProfiles = null;
}

