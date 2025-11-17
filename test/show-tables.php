<?php

require_once __DIR__."/../src/Loader.php";
require_once __DIR__."/../src/Document.php";

//require_once __DIR__."/Grt";
require_once __DIR__."/../src/Grt/Document.php";

require_once __DIR__."/../src/Grt_Object.php";
require_once __DIR__."/../src/Grt/NamedObject.php";
require_once __DIR__."/../src/Grt/StoredNote.php";
require_once __DIR__."/../src/Grt/Version.php";


//require_once __DIR__."/Grt/App";
require_once __DIR__."/../src/Grt/App/Document.php";
require_once __DIR__."/../src/Grt/App/DocumentInfo.php";
require_once __DIR__."/../src/Grt/App/PageSettings.php";
require_once __DIR__."/../src/Grt/App/PaperType.php";

//require_once __DIR__."/../src/Grt/Db";
require_once __DIR__."/../src/Grt/Db/CharacterSet.php";
require_once __DIR__."/../src/Grt/Db/CheckConstraint.php";
require_once __DIR__."/../src/Grt/Db/Column.php";
require_once __DIR__."/../src/Grt/Db/DatabaseObject.php";
require_once __DIR__."/../src/Grt/Db/DatabaseDdlObject.php";
require_once __DIR__."/../src/Grt/Db/Event.php";
require_once __DIR__."/../src/Grt/Db/ForeignKey.php";
require_once __DIR__."/../src/Grt/Db/Index.php";
require_once __DIR__."/../src/Grt/Db/IndexColumn.php";
require_once __DIR__."/../src/Grt/Db/Role.php";
require_once __DIR__."/../src/Grt/Db/RolePrivilege.php";
require_once __DIR__."/../src/Grt/Db/Schema.php";
require_once __DIR__."/../src/Grt/Db/Script.php";
require_once __DIR__."/../src/Grt/Db/SimpleDatatype.php";
require_once __DIR__."/../src/Grt/Db/Table.php";
require_once __DIR__."/../src/Grt/Db/User.php";
require_once __DIR__."/../src/Grt/Db/UserDatatype.php";
require_once __DIR__."/../src/Grt/Db/View.php";
require_once __DIR__."/../src/Grt/Db/Catalog.php";
require_once __DIR__."/../src/Grt/Db/LogFileGroup.php";
require_once __DIR__."/../src/Grt/Db/Routine.php";
require_once __DIR__."/../src/Grt/Db/RoutineGroup.php";
require_once __DIR__."/../src/Grt/Db/Sequence.php";
require_once __DIR__."/../src/Grt/Db/ServerLink.php";
require_once __DIR__."/../src/Grt/Db/StructuredDatatype.php";
require_once __DIR__."/../src/Grt/Db/Synonym.php";
require_once __DIR__."/../src/Grt/Db/Tablespace.php";
require_once __DIR__."/../src/Grt/Db/Trigger.php";


//require_once __DIR__."/../src/Grt/Db/Mysql";
require_once __DIR__."/../src/Grt/Db/Mysql/Catalog.php";
require_once __DIR__."/../src/Grt/Db/Mysql/Column.php";
require_once __DIR__."/../src/Grt/Db/Mysql/ForeignKey.php";
require_once __DIR__."/../src/Grt/Db/Mysql/Index.php";
require_once __DIR__."/../src/Grt/Db/Mysql/IndexColumn.php";
require_once __DIR__."/../src/Grt/Db/Mysql/LogFileGroup.php";
require_once __DIR__."/../src/Grt/Db/Mysql/PartitionDefinition.php";
require_once __DIR__."/../src/Grt/Db/Mysql/Routine.php";
require_once __DIR__."/../src/Grt/Db/Mysql/RoutineGroup.php";
require_once __DIR__."/../src/Grt/Db/Mysql/Schema.php";
require_once __DIR__."/../src/Grt/Db/Mysql/Sequence.php";
require_once __DIR__."/../src/Grt/Db/Mysql/ServerLink.php";
require_once __DIR__."/../src/Grt/Db/Mysql/StructuredDatatype.php";
require_once __DIR__."/../src/Grt/Db/Mysql/Synonym.php";
require_once __DIR__."/../src/Grt/Db/Mysql/Table.php";
require_once __DIR__."/../src/Grt/Db/Mysql/Tablespace.php";
require_once __DIR__."/../src/Grt/Db/Mysql/Trigger.php";
require_once __DIR__."/../src/Grt/Db/Mysql/View.php";
require_once __DIR__."/../src/Grt/Db/Mysql/RoutineParam.php";


//require_once __DIR__."/../src/Grt/Db/Mgmt";
require_once __DIR__."/../src/Grt/Db/Mgmt/Connection.php";
require_once __DIR__."/../src/Grt/Db/Mgmt/Rdbms.php";

//require_once __DIR__."/../src/Grt/Meta";
require_once __DIR__."/../src/Grt/Meta/Tag.php";

//require_once __DIR__."/../src/Grt/Model";
require_once __DIR__."/../src/Grt/Model_Object.php";
require_once __DIR__."/../src/Grt/Model/Connection.php";
require_once __DIR__."/../src/Grt/Model/Diagram.php";
require_once __DIR__."/../src/Grt/Model/Figure.php";
require_once __DIR__."/../src/Grt/Model/Group.php";
require_once __DIR__."/../src/Grt/Model/Layer.php";
require_once __DIR__."/../src/Grt/Model/Marker.php";
require_once __DIR__."/../src/Grt/Model/Model.php";

//require_once __DIR__."/../src/Grt/Workbench";
require_once __DIR__."/../src/Grt/Workbench/Document.php";
require_once __DIR__."/../src/Grt/Workbench/OverviewPanel.php";

//require_once __DIR__."/../src/Grt/Workbench/Logical";
require_once __DIR__."/../src/Grt/Workbench/Logical/Diagram.php";
require_once __DIR__."/../src/Grt/Workbench/Logical/Model.php";

require_once __DIR__."/../src/Grt/Workbench/Model/NoteFigure.php";
require_once __DIR__."/../src/Grt/Workbench/Model/ImageFigure.php";

require_once __DIR__."/../src/Grt/Workbench/Physical/Connection.php";
require_once __DIR__."/../src/Grt/Workbench/Physical/Diagram.php";
require_once __DIR__."/../src/Grt/Workbench/Physical/Layer.php";
require_once __DIR__."/../src/Grt/Workbench/Physical/Model.php";
require_once __DIR__."/../src/Grt/Workbench/Physical/TableFigure.php";
require_once __DIR__."/../src/Grt/Workbench/Physical/ViewFigure.php";
require_once __DIR__."/../src/Grt/Workbench/Physical/RoutineGroupFigure.php";


$filepath = realpath(__DIR__.'/../data/sakila_full.mwb');
$mwbDocument = \Mwb\Document::load($filepath);

foreach ($mwbDocument->doc->documentElement->physicalModels[0]->catalog->schemata[0]->tables as $table ) {
	echo $table->name . '(' . get_class($table) . ')' . PHP_EOL;
	foreach ($table->columns as $column) {
		echo ' - ' . $column->name . PHP_EOL;
	}
}

