<?php

namespace Mwb\Grt\Db\Mgmt;

/**
 * Relational Database Management System
 */
class Rdbms extends \Mwb\Grt_Object
{
    /**
     * @var $caption ?string the caption that is displayed in the UI
     */
    public ?string $caption = null;

    /**
     * @var $drivers ?\ArrayObject a list of drivers that can be used to connect to the
     * database system
     */
    public ?\ArrayObject $drivers = null;

    /**
     * @var $defaultDriver ?\Mwb\Grt\Db\Mgmt\Driver the default driver to use
     */
    public ?\Mwb\Grt\Db\Mgmt\Driver $defaultDriver = null;

    /**
     * @var $databaseObjectPackage ?string specifies the schema structs to use, e.g.
     * db.mysql
     */
    public ?string $databaseObjectPackage = null;

    /**
     * @var $simpleDatatypes ?\ArrayObject the list of simple datatypes the RDBMS
     * offers
     */
    public ?\ArrayObject $simpleDatatypes = null;

    /**
     * @var $characterSets ?\ArrayObject the list of character sets the RDBMS offers
     */
    public ?\ArrayObject $characterSets = null;

    /**
     * @var $version ?\Mwb\Grt\Version version of the catalog's database
     */
    public ?\Mwb\Grt\Version $version = null;

    /**
     * @var $privilegeNames ?\ArrayObject list of privilege names that are available in
     * this RDBMS
     */
    public ?\ArrayObject $privilegeNames = null;

    /**
     * @var $maximumIdentifierLength ?int maximum length for identifiers (schema,
     * table, column, index etc)
     */
    public ?int $maximumIdentifierLength = null;

    /**
     * @var $doesSupportCatalogs ?int Whether the RDBMS supports the notion of a
     * database catalog
     */
    public ?int $doesSupportCatalogs = null;
}

