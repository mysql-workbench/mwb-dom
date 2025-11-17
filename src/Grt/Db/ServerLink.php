<?php

namespace Mwb\Grt\Db;

class ServerLink extends \Mwb\Grt\Db\DatabaseObject
{
    public ?string $host = null;

    public ?string $ownerUser = null;

    public ?string $password = null;

    public ?string $port = null;

    public ?string $schema = null;

    public ?string $socket = null;

    public ?string $user = null;

    public ?string $wrapperName = null;
}

