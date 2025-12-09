<?php

namespace Mwb\Grt\Workbench\Model\Reporting;

/**
 * information about a model reporting template
 */
class TemplateStyleInfo extends \Mwb\Grt_Object
{
    /**
     * @var $name ?string the name of the style
     */
    public ?string $name = null;

    /**
     * @var $description ?string the note text
     */
    public ?string $description = null;

    /**
     * @var $styleTagValue ?string the value the style tag should be set to, e.g. the
     * name of the css file
     */
    public ?string $styleTagValue = null;

    /**
     * @var $previewImageFileName ?string the file name of the preview image, has to
     * match preview_*.png
     */
    public ?string $previewImageFileName = null;

    /**
     * @var $canUseHTMLMarkup ?int A flag that indicates if Workbench can use HTML
     * markup for SQL syntax highlighting.
     */
    public ?int $canUseHTMLMarkup = null;
}

