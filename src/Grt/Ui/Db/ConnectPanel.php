<?php

namespace Mwb\Grt\Ui\Db;

class ConnectPanel extends \Mwb\Grt\TransientObject
{
    /**
     * @var $connection ?\Mwb\Grt\Db\Mgmt\Connection
     */
    public ?\Mwb\Grt\Db\Mgmt\Connection $connection = null;

    /**
     * @var $view ?\Mwb\Grt\Mforms\ObjectReference reference to the toplevel mforms
     * View of the connect panel
     */
    public ?\Mwb\Grt\Mforms\ObjectReference $view = null;
}

