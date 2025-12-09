<?php

namespace Mwb\Grt\Workbench\Model\Reporting;

/**
 * information about a model reporting template
 */
class TemplateInfo extends \Mwb\Grt_Object
{
    /**
     * @var $name ?string the name of the template
     */
    public ?string $name = null;

    /**
     * @var $description ?string A short description of the type of template.
     */
    public ?string $description = null;

    /**
     * @var $styles ?\ArrayObject
     */
    public ?\ArrayObject $styles = null;

    /**
     * @var $mainFileName ?string the name of the file that will be opened after the
     * report has been created
     */
    public ?string $mainFileName = null;
}

