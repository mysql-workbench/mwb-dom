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

document.mwb.xml
```xml
<data grt_format="2.0" document_type="MySQL Workbench Model" version="1.4.4">
  <value type="object" struct-name="workbench.Document" id="362be786-bfc0-11ef-98ea-0242384af379">
    <value type="list" content-type="object" content-struct-name="workbench.physical.Model" key="physicalModels">
      <value type="object" struct-name="workbench.physical.Model" id="362be78a-bfc0-11ef-98ea-0242384af379">
        <value type="object" struct-name="db.mysql.Catalog" id="362be78b-bfc0-11ef-98ea-0242384af379" key="catalog">
          <value type="list" content-type="object" content-struct-name="db.mysql.Schema" key="schemata">
            <value type="object" struct-name="db.mysql.Schema" id="362be7aa-bfc0-11ef-98ea-0242384af379">
              <value type="list" content-type="object" content-struct-name="db.mysql.Table" key="tables">
                <value type="object" struct-name="db.mysql.Table" id="42948398-bfc0-11ef-98ea-0242384af379">
                  <value type="string" key="avgRowLength"/>
                  <value type="int" key="checksum">0</value>
                  <value type="list" content-type="object" content-struct-name="db.mysql.Column" key="columns">
                    ...
                  </value>
                  <value type="string" key="name">companies</value>
                </value>
                ...
```

var_dump
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

For example, the MwbDocument class allow to quickly access document.mwb.xml(MySQL Worbench model)
```PHP
<?php
$grt_root_wb_doc = \Mwb\Document::load('./sakila_full.mwb')->doc->documentElement;// Relative path use getcwd()
```

You can iterate through all tables from schema as you would do with MySQL Workbench Scripting Shell
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
$schema = \Mwb\Document::load(__DIR__.'/sakila_full.mwb')->doc->documentElement->physicalModels[0].catalog.schemata[0];
echo $schema->tables['users']->columns['id']->autoIncrement . PHP_EOL;
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


