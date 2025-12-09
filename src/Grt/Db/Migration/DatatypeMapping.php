<?php

namespace Mwb\Grt\Db\Migration;

/**
 * mapping of a datatype from one database to another
 */
class DatatypeMapping extends \Mwb\Grt_Object
{
    /**
     * @var $sourceDatatypeName ?string name of the datatype in the source database
     */
    public ?string $sourceDatatypeName = null;

    /**
     * @var $targetDatatypeName ?string name of the datatype in the target database
     */
    public ?string $targetDatatypeName = null;

    /**
     * @var $lengthConditionFrom ?int if set to a value different than 0 this becomes a
     * condition
     */
    public ?int $lengthConditionFrom = null;

    /**
     * @var $lengthConditionTo ?int if set to a value different than 0 this becomes a
     * condition
     */
    public ?int $lengthConditionTo = null;

    /**
     * @var $length ?int overwrite length if different than -2
     */
    public ?int $length = null;

    /**
     * @var $precisionConditionFrom ?int if set to a value different than 0 this
     * becomes a condition
     */
    public ?int $precisionConditionFrom = null;

    /**
     * @var $precisionConditionTo ?int if set to a value different than 0 this becomes
     * a condition
     */
    public ?int $precisionConditionTo = null;

    /**
     * @var $precision ?int overwrite precision if different than -2
     */
    public ?int $precision = null;

    /**
     * @var $scaleConditionFrom ?int if set to a value different than 0 this becomes a
     * condition
     */
    public ?int $scaleConditionFrom = null;

    /**
     * @var $scaleConditionTo ?int if set to a value different than 0 this becomes a
     * condition
     */
    public ?int $scaleConditionTo = null;

    /**
     * @var $scale ?int overwrite scale if different than -2
     */
    public ?int $scale = null;

    /**
     * @var $isUnsigned ?int sets the unsigned flag
     */
    public ?int $isUnsigned = null;
}

