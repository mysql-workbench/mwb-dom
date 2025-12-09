<?php

namespace Mwb\Grt\Workbench\Model;

/**
 * a model figure representing an image
 */
class ImageFigure extends \Mwb\Grt\Model\Figure
{
    /**
     * @var $filename ?string the image file name
     */
    public ?string $filename = null;

    /**
     * @var $keepAspectRatio ?int
     */
    public ?int $keepAspectRatio = null;
}

