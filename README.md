# A PHP DOM of MySQL Workbench (*.mwb)
The Document Object Model(DOM) connects MySQL Workbench model to scripts php by representing the structure of a document—such as the Class representing a Shema, EER diagram. Usually it refers to Shema, even though modeling EER Diagram, Layer, or View diagram as objects are part of the core of MySQL Workbenh software.

The DOM represents a document with a physical model. Each object of the model ends in a node, and each node contains objects. DOM methods allow programmatic access to the tree layer. With them, you can change the document's structure, style, or content.

Tables can also have event handlers attached to them. Once an event is triggered, the event handlers get executed.

## Prerequist
 - Php 7.2 >+
 - MySQL Workbench 6.3 | 8.0

## Install
```console
$ composer require mysql-workbench/mwb-dom
```

## Concepts
The Document Object Model (MwbDocument) is a programming interface for continious integration. It represents the database so that programs can change the document structure, style, and content. The DOM represents the document as properties and arrays; that way, PHP programming languages can interact with the page.
MywDocument model is a document that can be either displayed in the browser window or as the XML source. In both cases, it is the same document but the Document Object Model (MwbDocument) representation allows it to be manipulated. As an object-oriented representation of the database model, it can be modified with a PHP such as script.
For example, the MwbDocument allow to querySelectorAll method in this code snippet must return a list of all the Db_Table in the document:

```PHP
<?php

$filepath = __DIR__.'/data/sakila_full.mwb';
$mwbDocument = \Mwb\Document::load($filepath);

foreach ($mwbDocument->doc->documentElement->physicalModels[0]->catalog->schemata[0]->tables as $table ) {
  echo $table->name . '(' . gettype($table->owner) . ')' . PHP_EOL;
  foreach ($table->columns as $column) {
    echo ' - ' . $column->name . PHP_EOL;
  }
}

```

## Usage
Use `mysql-workbench/mwb-orm` instead of `mysql-workbench/mwb-dom` and improve `\Mwb` :
```PHP
<?php

$filepath = __DIR__.'/data/model.mwb';
$mwbDocument = \Mwb\Document::load($filepath);

foreach ($mwbDocument->getEntities() as $entity ) {
  echo $entity->getName() . PHP_EOL;
  echo $entity->getTable()->name . PHP_EOL;
}
```


