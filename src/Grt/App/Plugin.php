<?php

namespace Mwb\Grt\App;

use \ArrayObject;

class Plugin extends \Mwb\Grt_Object
{
    public ?\ArrayObject $attributes = null;

    public ?string $caption = null;

    public ?string $description = null;

    public ?\ArrayObject $documentStructNames = null;

    public ?\ArrayObject $groups = null;

    public ?\ArrayObject $inputValues = null;

    public ?string $moduleFunctionName = null;

    public ?string $moduleName = null;

    public ?string $pluginType = null;

    public ?int $rating = null;

    public ?int $showProgress = null;
}

