<?php

namespace Mwb\Grt\Db\Oracle;

class ServerLink extends \Mwb\Grt\Db\ServerLink
{
    /**
     * @var $shared ?int specifies if the server link is shared
     */
    public ?int $shared = null;

    /**
     * @var $public ?int specifies if the server link is public
     */
    public ?int $public = null;

    /**
     * @var $currentUser ?int if set to 1, the current user account will used to make
     * the connection. The fields user and password are ignored in that case
     */
    public ?int $currentUser = null;

    /**
     * @var $connectionString ?string the owner
     */
    public ?string $connectionString = null;
}

