<?php

require_once __DIR__."/../src/Document.php";

$filepath = realpath(__DIR__.'/../data/aventurine.mwb');
$mwbDocument = \Mwb\Document::load($filepath);

foreach ($mwbDocument->doc->documentElement->physicalModels[0]->catalog->schemata[0]->tables as $table ) {
	echo $table->name . '(' . gettype($table->owner) . ')' . PHP_EOL;
	foreach ($table->columns as $column) {
		echo ' - ' . $column->name . PHP_EOL;
	}
}

