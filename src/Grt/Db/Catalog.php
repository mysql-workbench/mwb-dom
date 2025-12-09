<?php

namespace Mwb\Grt\Db;

class Catalog extends \Mwb\Grt\NamedObject
{
    /**
     * @var $defaultCharacterSetName ?string
     */
    public ?string $defaultCharacterSetName = null;

    /**
     * @var $defaultCollationName ?string
     */
    public ?string $defaultCollationName = null;

    /**
     * @var $schemata ?\ArrayObject
     */
    public ?\ArrayObject $schemata = null;

    /**
     * @var $simpleDatatypes ?\ArrayObject
     */
    public ?\ArrayObject $simpleDatatypes = null;

    /**
     * @var $userDatatypes ?\ArrayObject
     */
    public ?\ArrayObject $userDatatypes = null;

    /**
     * @var $characterSets ?\ArrayObject
     */
    public ?\ArrayObject $characterSets = null;

    /**
     * @var $version ?\Mwb\Grt\Version version of the catalog's database
     */
    public ?\Mwb\Grt\Version $version = null;

    /**
     * @var $customData ?\ArrayObject a generic dictionary to hold additional
     * information used by e.g. plugins
     */
    public ?\ArrayObject $customData = null;

    /**
     * @var $defaultSchema ?\Mwb\Grt\Db\Schema currently selected schema
     */
    public ?\Mwb\Grt\Db\Schema $defaultSchema = null;

    /**
     * @var $logFileGroups ?\ArrayObject
     */
    public ?\ArrayObject $logFileGroups = null;

    /**
     * @var $tablespaces ?\ArrayObject
     */
    public ?\ArrayObject $tablespaces = null;

    /**
     * @var $serverLinks ?\ArrayObject
     */
    public ?\ArrayObject $serverLinks = null;

    /**
     * @var $roles ?\ArrayObject
     */
    public ?\ArrayObject $roles = null;

    /**
     * @var $users ?\ArrayObject
     */
    public ?\ArrayObject $users = null;
}

