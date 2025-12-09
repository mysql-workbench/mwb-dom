<?php

namespace Mwb\Grt\Db\Mgmt;

/**
 * a list of all parameters the Jdbc driver supports
 */
class DriverParameter extends \Mwb\Grt_Object
{
    /**
     * @var $caption ?string the caption displayed in the connection dialog
     */
    public ?string $caption = null;

    /**
     * @var $description ?string the description displayed in the connection dialog
     */
    public ?string $description = null;

    /**
     * @var $paramType ?string can be string, int, boolean, tristate, file, dir
     */
    public ?string $paramType = null;

    /**
     * @var $paramTypeDetails ?\ArrayObject additional information e.g. like file
     * extension
     */
    public ?\ArrayObject $paramTypeDetails = null;

    /**
     * @var $required ?int if set to 1 this parameter is a required parameter
     */
    public ?int $required = null;

    /**
     * @var $defaultValue ?string the default value of the parameter
     */
    public ?string $defaultValue = null;

    /**
     * @var $lookupValueModule ?string the module that contains the method to call to
     * get the list of possible values
     */
    public ?string $lookupValueModule = null;

    /**
     * @var $lookupValueMethod ?string the method to call to get the list of possible
     * values
     */
    public ?string $lookupValueMethod = null;

    /**
     * @var $layoutAdvanced ?int when set to 1 this is paramter is only displayed in
     * the advanced parameter section
     */
    public ?int $layoutAdvanced = null;

    /**
     * @var $layoutRow ?int the row the parameter is displayed. There can be more than
     * one parameters on the same row. When set to -1 the parameter is appended at the
     * end of the parameter list
     */
    public ?int $layoutRow = null;

    /**
     * @var $layoutWidth ?int the width of the edit
     */
    public ?int $layoutWidth = null;
}

