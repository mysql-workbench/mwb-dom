<?php

namespace Mwb\Grt\App;

class PluginFileInput extends \Mwb\Grt\App\PluginInputDefinition
{
    /**
     * @var $dialogTitle ?string Title to use in file dialog when requesting a file to
     * the user
     */
    public ?string $dialogTitle = null;

    /**
     * @var $dialogType ?string Type of file dialog (save, open)
     */
    public ?string $dialogType = null;

    /**
     * @var $fileExtensions ?string Accepted file extensions, starting with the default
     * one  (without the .)
     */
    public ?string $fileExtensions = null;
}

