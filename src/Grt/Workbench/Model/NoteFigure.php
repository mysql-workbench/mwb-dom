<?php

namespace Mwb\Grt\Workbench\Model;

/**
 * a model figure representing a text box
 */
class NoteFigure extends \Mwb\Grt\Model\Figure
{
    /**
     * @var $text ?string the text contents
     */
    public ?string $text = null;

    /**
     * @var $textColor ?string the text color
     */
    public ?string $textColor = null;

    /**
     * @var $font ?string the font to be used for the note
     */
    public ?string $font = null;
}

