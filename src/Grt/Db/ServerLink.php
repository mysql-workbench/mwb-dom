<?php

namespace Mwb\Grt\Db;

class ServerLink extends \Mwb\Grt\Db\DatabaseObject
{
    /**
     * @var $wrapperName ?string the type of database server to connect to
     */
    public ?string $wrapperName = null;

    /**
     * @var $user ?string the user to connect with
     */
    public ?string $user = null;

    /**
     * @var $password ?string the password to connect with
     */
    public ?string $password = null;

    /**
     * @var $host ?string the host name the server runs on
     */
    public ?string $host = null;

    /**
     * @var $schema ?string the name of the schema to use
     */
    public ?string $schema = null;

    /**
     * @var $socket ?string the socket the server runs on
     */
    public ?string $socket = null;

    /**
     * @var $port ?string the port the server runs on
     */
    public ?string $port = null;

    /**
     * @var $ownerUser ?string the owner
     */
    public ?string $ownerUser = null;
}

