<?php

require_once __DIR__."/../vendor/autoload.php";

$filepath = realpath(__DIR__.'/../data/sakila_full.mwb');
$mwbDocument = \Mwb\Document::Load($filepath);

foreach ($mwbDocument->doc->documentElement->physicalModels[0]->catalog->schemata[0]->tables as $table ) {
	echo $table->name . '(' . get_class($table) . ')' . PHP_EOL;
	foreach ($table->columns as $column) {
		echo ' - ' . $column->name . PHP_EOL;
	}
}

