<?php

class Grt_Object {
  //protected object $owner;
}

class Grt_NamedObject extends Grt_Object {
  public ?string $name = Null;
}

class Grt_DatabaseObject extends Grt_NamedObject {
  public ?string $comment = Null;
}

// ---------------------

/**
 * @template T of Grt_Table
 */
interface OwnedByTable {
    /**
     * @return T
     */
    public function getOwner(): Grt_Table;

    /**
     * @param T $owner
     */
    public function setOwner(Grt_Table $owner): void;
}

/**
 * @template T of Grt_Table
 * @implements OwnedByTable<T>
 */
class Grt_ForeignKey extends Grt_DatabaseObject implements OwnedByTable {
    /** @var T */
    protected Grt_Table $owner;

    /** @return T */
    public function getOwner(): Grt_Table {
        return $this->owner;
    }

    /** @param T $owner */
    public function setOwner(Grt_Table $owner): void {
        $this->owner = $owner;
    }
}

class Grt_Table extends Grt_DatabaseObject {
    /** @var Grt_ForeignKey[] */
    public array $foreignKeys = [];
}


// ---------------------

/**
 * @extends Grt_ForeignKey<Mysql_Table>
 */
class Mysql_ForeignKey extends Grt_ForeignKey {
    // Aucun override !
}

/**
 * @property Mysql_ForeignKey[] $foreignKeys
 */
class Mysql_Table extends Grt_Table {
    /** @var Mysql_ForeignKey[] */
    public array $foreignKeys = [];
}
// ===================================

$table = new Mysql_Table();



