<?php

namespace Mwb\Grt\Db;

use \ArrayObject;
use \Mwb\Grt\Db\Schema;
use \Mwb\Grt\Version;

class Catalog extends \Mwb\Grt\NamedObject
{
    public ?\ArrayObject $characterSets = null;

    public ?\ArrayObject $customData = null;

    public ?string $defaultCharacterSetName = null;

    public ?string $defaultCollationName = null;

    public ?\Mwb\Grt\Db\Schema $defaultSchema = null;

    public ?\ArrayObject $logFileGroups = null;

    public ?\ArrayObject $roles = null;

    public ?\ArrayObject $schemata = null;

    public ?\ArrayObject $serverLinks = null;

    public ?\ArrayObject $simpleDatatypes = null;

    public ?\ArrayObject $tablespaces = null;

    public ?\ArrayObject $userDatatypes = null;

    public ?\ArrayObject $users = null;

    public ?\Mwb\Grt\Version $version = null;
}

