<?php

declare(strict_types=1);

namespace MwbTest;

use Mwb\Document;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Mwb\Document;
 */
class MwbDocumentTest extends TestCase
{
	public function testNew(): void
	{
		$owner = new \Mwb\Document();

		self::assertTrue($owner instanceof \Mwb\Document);
	}

	public function testNewGrt(): void
	{
		$dbObject = new \Mwb\Grt\Db\DatabaseObject();
		self::assertTrue($dbObject instanceof \Mwb\Grt\Db\DatabaseObject);
		self::assertTrue($dbObject instanceof \Mwb\Grt_Object);

		//$table = new \Mwb\Grt\Db\Table();
		//self::assertTrue($table instanceof \Mwb\Grt\Db\Table);
	}

	public function testLoad(): void
	{
		$filepath = realpath(__DIR__.'/../data/sakila_full.mwb');
/*
		$mwbDocument = new \Mwb\Document();
		$mwbDocument->load($filepath);

		//$mwbDocument = \Mwb\Document::Load($filepath);

		self::assertTrue($mwbDocument instanceof \Mwb\Document);
		self::assertTrue($mwbDocument->doc instanceof \Mwb\Grt\Document);
		self::assertTrue(1==count($mwbDocument->doc->documentElement->physicalModels));
		self::assertTrue(1==count($mwbDocument->doc->documentElement->physicalModels[0]->catalog->schemata));
		self::assertTrue(16==count($mwbDocument->doc->documentElement->physicalModels[0]->catalog->schemata[0]->tables));
*/

/*
		foreach ($mwbDocument->doc->documentElement->physicalModels[0]->catalog->schemata[0]->tables as $table ) {
			echo $table->name . '(' . get_class($table) . ')' . PHP_EOL;
			foreach ($table->columns as $column) {
				echo ' - ' . $column->name . PHP_EOL;
			}
		}
*/
	}
}


