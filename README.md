# A PHP DOM of MySQL Workbench (*.mwb)
The Document Object Model(DOM) connects MySQL Workbench model to scripts php by representing the structure of a document—such as the Class representing a Shema, EER diagram. Usually it refers to Shema, even though modeling EER Diagram, Layer, or View diagram as objects are part of the core of MySQL Workbenh software.

## A PHP binding of MySQL Workbench
The DOM represents a document with a physical model. Each object of the model ends in a node, and each node contains objects. DOM methods allow programmatic access to the tree layer. With them, you can change the document's structure, style, or content.

-- To be continue : Tables can also have event handlers attached to them. Once an event is triggered, the event handlers get executed. --

## Prerequist
 - Php 8.0 >+
 - MySQL Workbench 6.3 | 8.0

## Install
```console
$ composer require mysql-workbench/mwb-dom "^1.0.0"
```

## Concepts
The Document Object Model (MwbDocument) is a programming interface for continious integration. It represents the database so that programs can change the document content. The DOM represents the document as properties and arrays; that way, PHP programming language can interact with the document.

For example, the MwbDocument class allow to query all `Db\Table` in the document:

```PHP
<?php
$grt_root_wb_doc = \Mwb\Document::load(__DIR__.'/sakila_full.mwb')->doc->documentElement;

$schema = $grt_root_wb_doc.physicalModels[0].catalog.schemata[0]
foreach ($schema->tables as $table ) {
  print $table->name . PHP_EOL;
}
```

  foreach ($table->columns as $column) {
    echo ' - ' . $column->name . PHP_EOL;
  }


As you would do with MySQL Workbench Scripting Shell
```python
# iterate through all tables from schema
schema = grt.root.wb.doc.physicalModels[0].catalog.schemata[0]
for table in schema.tables:
    print table.name
```

Another example for easily accessing your model
```php
<?php
$schema = \Mwb\Document::load(__DIR__.'/sakila_full.mwb')->doc->documentElement->physicalModels[0].catalog.schemata[0];
echo $schema->tables['users']->columns['id']->name . PHP_EOL;// `id`
```


## Usage
Improve the use of `mysql-workbench/mwb-dom` by using `mysql-workbench/mwb-orm` :
```PHP
<?php

$filepath = __DIR__.'/data/model.mwb';
$mwbOrm = \Mwb\Orm\Document::load($filepath);

foreach ($mwbOrm->getEntities() as $entity ) {
  echo $entity->getName() . PHP_EOL;
  echo $entity->getTable()->name . PHP_EOL;
}
```


