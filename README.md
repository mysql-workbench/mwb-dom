# Description

## A PHP DOM of MySQL Workbench (*.mwb)
The Document Object Model(DOM) connects MySQL Workbench model to scripts php by representing the structure of a document—such as the Class representing a Shema, EER diagram. Usually it refers to Shema, even though modeling EER Diagram, Layer, or View diagram as objects are part of the core of MySQL Workbenh software.

## A PHP binding of MySQL Workbench
The Document Object Model (MwbDocument) is a programming interface for continious integration. It represents the database so that programs can change the document content. The DOM represents the document as properties and arrays; that way, PHP programming language can interact with the document.

### Origin
The `mysql-workbench/mwb-dom` library was created using [reverse engineering](https://github.com/mysql-workbench/mwb-reverse-engineering) of the MySQL Workbench source code.
Therefore, the methods and functions exposed for Python in MySQL Workbench are present in PHP.

-- To be continue : Tables can also have event handlers attached to them. Once an event is triggered, the event handlers get executed. --

## Prerequist
 - Php 8.0 >+
 - MySQL Workbench 6.3 | 8.0

## Install
```console
$ composer require mysql-workbench/mwb-dom "^1.0.0"
```

## Concepts
The DOM represents a document with a physical model. Each object of the model ends in a node, and each node contains objects. DOM methods allow programmatic access to the tree layer. With them, you can change the document's structure, style, or content.

The `Mwb\Loader` class create an instance(of \ArrayObject or \Mwb\Grt\*) for each `<value >` using struct-name as class
### document.mwb.xml
```xml
<data grt_format="2.0" document_type="MySQL Workbench Model" version="1.4.4">
  <value struct-name="workbench.Document">
    <value type="list" key="physicalModels">
      <value struct-name="workbench.physical.Model" >
        <value struct-name="db.mysql.Catalog" key="catalog">
          <value type="list" key="schemata">
            <value struct-name="db.mysql.Schema">
              <value type="list" key="tables">
                <value struct-name="db.mysql.Table">
                  <value type="string" key="avgRowLength"/>
                  <value type="int" key="checksum">0</value>
                  <value type="list" key="columns">
                    ...
                  </value>
                  <value type="string" key="name">companies</value>
                </value>
                ...
```

### var_dump
```php
object(\Mwb\Grt\Document)#1 (2) {
  ["documentElement"]=> object(\Mwb\Grt\Workbench\Document)#2 {
    ["physicalModels"]=> array()#3 [
      [0]=> object(\Mwb\Grt\Workbench\Physical\Model)#4 {
        ["catalog"]=> object(\Mwb\Grt\Db\Mysql\Catalog)#5 {
          ["schemata"]=> array()#6 [
            [0]=> object(\Mwb\Grt\Db\Mysql\Schema)#7 {
              ["tables"]=> array()#8 [
                [0]=> object(\Mwb\Grt\Db\Mysql\Table)#9 {
                  ["avgRowLength"]=> string()#
                  ["checksum"]=> int()
                  ["columns"]=> array()# [
                    ...
                  ]
                  ["name"]=> string(9) "companies"
                ...
```
## Usage

For example, the `\Mwb\Document` class allow to quickly access document.mwb.xml(MySQL Worbench model)
```PHP
<?php
$grt_root_wb_doc = \Mwb\Document::load('/sakila_full.mwb')->doc->documentElement;// Relative path use getcwd()
```

You can iterate through all tables from schema as you would do with [MySQL Workbench Scripting Shell](https://dev.mysql.com/doc/workbench/en/wb-exploring-scripting-shell.html)
```PHP
<?php
$schema = $grt_root_wb_doc.physicalModels[0].catalog.schemata[0]
foreach ($schema->tables as $table ) {
  print $table->name . PHP_EOL;
}
```
Another example for easily accessing data.
```php
<?php
$schema = $grt_root_wb_doc.physicalModels[0].catalog.schemata[0]
echo $schema->tables['users']->columns['id']->autoIncrement . PHP_EOL;
```


## Go further
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


