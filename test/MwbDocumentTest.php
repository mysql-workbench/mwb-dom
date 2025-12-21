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

		$mwbDocument = new \Mwb\Document();
		$mwbDocument->load($filepath);

		//$mwbDocument = \Mwb\Document::Load($filepath);

		self::assertTrue($mwbDocument instanceof \Mwb\Document);
		self::assertTrue($mwbDocument->doc instanceof \Mwb\Grt\Document);
		self::assertTrue(1==count($mwbDocument->doc->documentElement->physicalModels));
		self::assertTrue(1==count($mwbDocument->doc->documentElement->physicalModels[0]->catalog->schemata));
		self::assertTrue(16==count($mwbDocument->doc->documentElement->physicalModels[0]->catalog->schemata[0]->tables));

		$table = $mwbDocument->doc->documentElement->physicalModels[0]->catalog->schemata[0]->tables['city'];
		self::assertTrue($table instanceof \Mwb\Grt\Db\Mysql\Table);
		self::assertTrue(4==count($table->columns));

		//print_r(array_keys((array)$table->columns));
		$column = $table->columns['city_id'];
		self::assertTrue($column instanceof \Mwb\Grt\Db\Mysql\Column);

		$simpleType = $column->simpleType;
		self::assertTrue($simpleType instanceof \Mwb\Grt\Db\SimpleDatatype);
		//var_dump($simpleType->name);
		//var_dump(get_class($simpleType->owner));
		//var_dump($simpleType->owner->name);// "Mysql" => com.mysql.rdbms.mysql
		//var_dump(gettype($simpleType->simpleDatatypes));


		//var_dump(gettype($simpleType));
		//print_r(array_keys((array)$column));
		//$column->owner = Null;
		//var_dump($column);

/*
		foreach ($mwbDocument->doc->documentElement->physicalModels[0]->catalog->schemata[0]->tables as $table ) {
			echo $table->name . '(' . get_class($table) . ')' . PHP_EOL;
			foreach ($table->columns as $column) {
				echo ' - ' . $column->name . PHP_EOL;
			}
		}
*/
	}

	/*
	 * $loader = new Loader();
	 *
	 * $loader->loadXml('/usr/share/mysql-workbench/data/db_datatype_groups.xml');
	 *
	 * $loader->loadXml('/usr/share/mysql-workbench/modules/data/generic_rdbms_info.xml');
	 * $loader->loadXml('/usr/share/mysql-workbench/modules/data/mssql_rdbms_info.xml');
	 * $loader->loadXml('/usr/share/mysql-workbench/modules/data/mysql_rdbms_info.xml');
	 * $loader->loadXml('/usr/share/mysql-workbench/modules/data/postgresql_rdbms_info.xml');
	 * $loader->loadXml('/usr/share/mysql-workbench/modules/data/sql92_rdbms_info.xml');
	 * $loader->loadXml('/usr/share/mysql-workbench/modules/data/sqlanywhere_rdbms_info.xml');
	 * $loader->loadXml('/usr/share/mysql-workbench/modules/data/sqlite_rdbms_info.xml');
	 * $loader->loadXml('/usr/share/mysql-workbench/modules/data/sybase_rdbms_info.xml');
	 * 
	 * $loader->loadXml('document.mwb.xml');
	public function testLoader(): void
	{
		$loader = new \Mwb\Loader();
		$xml = simplexml_load_file(__DIR__.'/../../mwb-reverse-engineering/data/6.3/'.'/usr/share/mysql-workbench/data/db_datatype_groups.xml');
		$loader->loadNodeXml($xml);

		self::assertTrue(True);
	}
	 */
}


