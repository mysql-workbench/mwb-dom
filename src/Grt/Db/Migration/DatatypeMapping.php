<?php

namespace Mwb\Grt\Db\Migration;

class DatatypeMapping extends \Mwb\Grt_Object
{
    public ?int $isUnsigned = null;

    public ?int $length = null;

    public ?int $lengthConditionFrom = null;

    public ?int $lengthConditionTo = null;

    public ?int $precision = null;

    public ?int $precisionConditionFrom = null;

    public ?int $precisionConditionTo = null;

    public ?int $scale = null;

    public ?int $scaleConditionFrom = null;

    public ?int $scaleConditionTo = null;

    public ?string $sourceDatatypeName = null;

    public ?string $targetDatatypeName = null;
}

