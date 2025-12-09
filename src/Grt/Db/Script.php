<?php

namespace Mwb\Grt\Db;

/**
 * a SQL script
 */
class Script extends \Mwb\Grt\StoredNote
{
    /**
     * @var $forwardEngineerScriptPosition ?string
     */
    public ?string $forwardEngineerScriptPosition = null;

    /**
     * @var $synchronizeScriptPosition ?string
     */
    public ?string $synchronizeScriptPosition = null;
}

