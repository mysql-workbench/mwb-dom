<?php

namespace Mwb\Grt\Db;

class ForeignKey extends \Mwb\Grt\NamedObject
{
    /**
     * @var $owner ?\Mwb\Grt\Db\Table
     */
    public ?\Mwb\Grt_Object $owner = null;

    /**
     * @var $modelOnly ?int
     */
    public ?int $modelOnly = null;

    /**
     * @var $deleteRule ?string
     */
    public ?string $deleteRule = null;

    /**
     * @var $updateRule ?string
     */
    public ?string $updateRule = null;

    /**
     * @var $deferability ?int
     */
    public ?int $deferability = null;

    /**
     * @var $referencedTable ?\Mwb\Grt\Db\Table
     */
    public ?\Mwb\Grt\Db\Table $referencedTable = null;

    /**
     * @var $columns ?\ArrayObject
     */
    public ?\ArrayObject $columns = null;

    /**
     * @var $referencedColumns ?\ArrayObject
     */
    public ?\ArrayObject $referencedColumns = null;

    /**
     * @var $mandatory ?int mandatory in the owner table
     */
    public ?int $mandatory = null;

    /**
     * @var $referencedMandatory ?int mandatory in the referenced table
     */
    public ?int $referencedMandatory = null;

    /**
     * @var $many ?int cardinality of owner table
     */
    public ?int $many = null;

    /**
     * @var $index ?\Mwb\Grt\Db\Index Index that was created for this Foreign Key. This
     * should only be set when a index is created for the FK, in other cases (like on
     * reverse engieer) it should be left unset. For that reason this should not be
     * used to find the matching index for the FK.
     */
    public ?\Mwb\Grt\Db\Index $index = null;

    /**
     * @var $customData ?\ArrayObject
     */
    public ?\ArrayObject $customData = null;
}

