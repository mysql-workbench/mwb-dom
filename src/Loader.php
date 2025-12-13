<?php

declare(strict_types=1);



namespace Mwb;


class Loader {

	const MWB_NAMESPACE = '\\Mwb\\';
	const GRT_NAMESPACE = self::MWB_NAMESPACE . 'Grt\\';
	public $ids = [];// instances
	//public $doc;
	public $links = [];// array ['xml'=>$xml, 'objectGrt'=>$objectGrt]

	static protected function Naming($objectName) {
		$names = explode('.', $objectName);
		$names = array_map('ucfirst', $names);
		return implode('\\', $names);
	}

	static private function ResolveClass($structName) {
		if ('GrtVersion'==$structName) {
			$structName = 'Version';
		} else if ('GrtStoredNote'==$structName) {
			$structName = 'StoredNote';
		} else if ('GrtObject'==$structName) {
			$structName = 'Grt_Object';
			return self::MWB_NAMESPACE . self::Naming($structName);
		}
		$className = self::GRT_NAMESPACE . self::Naming($structName);
		return $className;
	}

	static private function Factory($className, $id) {
		$instance = null;
		$instance = new $className();
		return $instance;
	}

	private function getInstanceByXml($xml) {
		$id = !empty($xml['id']) ? (string)$xml['id'] : null;
		$structName = !empty($xml['struct-name']) ? (string)$xml['struct-name'] : null;

		$instance = Null;

		if (isset($this->ids[$id])) {
			$instance = $this->ids[$id];
		} else {
			$className = self::ResolveClass($structName);
			$instance = self::Factory($className, $id);
			$this->ids[$id] = $instance;
		}

		return $instance;
	}
	private function getInstanceById($id) {
		$instance = Null;
		if (isset($this->ids[$id])) {
			$instance = $this->ids[$id];
		}

		return $instance;
	}
	private function setInstance($id, $objectGrt) {
		$this->ids[$id] = $objectGrt;
	}

	public $resume = '';
	public $indent = '';

	public function loadValueXmlObject($xml, $parentObjectGrt=null) {
		$objectGrt = null;
		//$key        = !empty($xml['key'])         ? (string)$xml['key']           : null;
		$type       = !empty($xml['type'])        ? (string)$xml['type']          : null;
		$structName = !empty($xml['struct-name']) ? (string)$xml['struct-name']   : null;
		$id         = !empty($xml['id'])          ? (string)$xml['id']            : null;

		$objectGrt = $this->getInstanceByXml($xml);

		return $objectGrt;
	}

	public function loadValueXml($xml, $parentObjectGrt=null) {
		$objectGrt = null;
		$type = !empty($xml['type']) ? (string)$xml['type'] : null;
		$key = !empty($xml['key']) ? (string)$xml['key'] : null;
		switch ($type) {
			case 'object':
				$objectGrt = $this->loadValueXmlObject($xml, $parentObjectGrt);
				break;
			case 'list':
				$objectGrt = new \ArrayObject();
				break;
			case 'dict':
				$objectGrt = new \ArrayObject();
				break;
			case 'string':
				$objectGrt = (string) $xml;
				break;
			case 'int':
				$objectGrt = (int)((string) $xml);
				break;
			case 'real':
				$objectGrt = (float)((string) $xml);
				break;
			default :
				throw new \Exception("Something whent wrong : " . $type);
				break;
		}
		return $objectGrt;
	}

	public function loadLinkXml($xml, $parentObjectGrt=null) {
		$objectGrt = null;
		$type = !empty($xml['type']) ? (string)$xml['type'] : null;
		$key = !empty($xml['key']) ? (string)$xml['key'] : null;
		$struct_name = !empty($xml['struct-name']) ? (string)$xml['struct-name'] : null;

		$id = (string) $xml;

		$this->links[] = ['id'=>$id, 'xml'=>$xml, 'parentObjectGrt'=>$parentObjectGrt];

		return $objectGrt;
	}

	public function loadDataXml($xml) {
		$objectGrt = null;
		// check version

		$objectGrt = new \Mwb\Grt\Document();
		$objectGrt->documentElement = $this->loadNodeXml($xml->value, $objectGrt);
		$objectGrt->documentElement->owner = Null;
		// check owner ?

		return $objectGrt;
	}

	public $count = 0;
	protected function loadNodeXml($xml, $parentObjectGrt=null) {
		$this->count++;
		$this->indent = str_pad(' ', $this->count*4);

		$objectGrt = null;
		$type = !empty($xml['type']) ? (string)$xml['type'] : null;

		if ('value'==$xml->getName()) {
			$objectGrt = $this->loadValueXml($xml, $parentObjectGrt);
		} else if('data'==$xml->getName()) {
			$objectGrt = $this->loadDataXml($xml, $parentObjectGrt);
		} else if('link'==$xml->getName()) {
			$objectGrt = $this->loadLinkXml($xml, $parentObjectGrt);
		} else {
			throw new \Exception("Unexpected reach");
		}

		foreach ($xml->children() as $nodeXml)
		{
			$nodeObjectGrt = null;
			$nodeType = !empty($nodeXml['type']) ? (string)$nodeXml['type'] : null;
			$nodeKey = !empty($nodeXml['key']) ? (string)$nodeXml['key'] : null;

			$nodeObjectGrt = $this->loadNodeXml($nodeXml, $objectGrt);
			switch ($type) {
				case 'object':
					$objectGrt->$nodeKey = $nodeObjectGrt;
					break;
				case 'dict':
					$objectGrt[$nodeKey] = $nodeObjectGrt;
					break;
				case 'list':
					if ($nodeObjectGrt
					    && ($parentObjectGrt instanceof  \Mwb\Grt\Db\Schema
					    ||  $parentObjectGrt instanceof  \Mwb\Grt\Db\Table )
					) {
						$objectGrt[$nodeObjectGrt->name] = $nodeObjectGrt;
					} else if ($parentObjectGrt instanceof  \Mwb\Grt\Db\Column) {
						//if ('string'==gettype($nodeObjectGrt));
						$objectGrt[$nodeObjectGrt] = $nodeObjectGrt;
					} else {
						$objectGrt[count($objectGrt)] = $nodeObjectGrt;
					}
					break;
				default:
					if ($objectGrt instanceof \Mwb\Grt\Document) {
						// ->documentElement has no <value key="">
					} else {
						echo 'Unexpected type : '.gettype($type).', '.$type.PHP_EOL;
					}
					break;
			}
			/*
			$key = !empty($nodeXml['key']) ? (string)$nodeXml['key'] : null;
			$objectGrt->$key = $childObjectGrt;
			*/
		}


		$this->count--;
		$this->indent = str_pad(' ', $this->count*4);

		return $objectGrt;
	}

	public function loadLinkXmlObject($xml, $parentObjectGrt=null) {
		foreach($this->links as $link) {
			$id = $link['id'];
			$xml = $link['xml'];
			$parentObjectGrt = $link['parentObjectGrt'];

			$key = $xml['key'];
			if (empty($key)) {
				if ($parentObjectGrt instanceof \ArrayObject) {
					/*
					      <value _ptr_="0x55a0c4654ef0" type="list" content-type="object" content-struct-name="db.Column" key="columns">
						<link type="object">ec729f76-bfc0-11ef-98ea-0242384af379</link>
					      </value>
					*/
					$parentObjectGrt[] = $this->getInstanceById($id);
				} else {
					echo 'Unexpected case : unreachable'.PHP_EOL;
				}
			} else {
				//<link type="object" struct-name="model.Layer" key="layer">cf7f3d82-0108-11f0-89a7-02428f196987</link>
				$parentObjectGrt->$key = $this->getInstanceById($id);
			}
		}
	}

	// {{PLACEHOLDER}}
	private function com_mysql_wb_papertype_Load()
	{
		$paperTypes = [
			'com.mysql.wb.papertype.letter' => new \Mwb\Grt\App\PaperType([
				'name' => 'na-letter', 
				'caption' => 'Letter (8.5 in. x 11 in.)', 
				'width' => 215.9, 
				'height' => 279.4
			]), 
			'com.mysql.wb.papertype.legal' => new \Mwb\Grt\App\PaperType([
				'name' => 'na-legal', 
				'caption' => 'Legal (8.5 in. x 14 in.)', 
				'width' => 215.9, 
				'height' => 355.6
			]), 
			'com.mysql.wb.papertype.folio' => new \Mwb\Grt\App\PaperType([
				'name' => 'folio', 
				'caption' => 'Folio (8.5 in. x 13 in.)', 
				'width' => 215.9, 
				'height' => 330.2
			]), 
			'com.mysql.wb.papertype.a4' => new \Mwb\Grt\App\PaperType([
				'name' => 'iso-a4', 
				'caption' => 'A4 (210 mm x 297 mm)', 
				'width' => 209.9, 
				'height' => 297
			]), 
			'com.mysql.wb.papertype.a5' => new \Mwb\Grt\App\PaperType([
				'name' => 'iso-a5', 
				'caption' => 'A5 (148 mm x 210 mm)', 
				'width' => 148.2, 
				'height' => 209.9
			]), 
			'com.mysql.wb.papertype.b4' => new \Mwb\Grt\App\PaperType([
				'name' => 'iso-b4', 
				'caption' => 'B4 (250 mm x 353 mm)', 
				'width' => 250.1, 
				'height' => 353
			]), 
			'com.mysql.wb.papertype.b5' => new \Mwb\Grt\App\PaperType([
				'name' => 'iso-b5', 
				'caption' => 'B5 (176 mm x 250 mm)', 
				'width' => 176, 
				'height' => 250.1
			]), 
			'com.mysql.wb.papertype.executive' => new \Mwb\Grt\App\PaperType([
				'name' => 'executive', 
				'caption' => 'Executive (7.5 in. x 10.5 in.)', 
				'width' => 184.2, 
				'height' => 266.7
			]), 
			'com.mysql.wb.papertype.statement' => new \Mwb\Grt\App\PaperType([
				'name' => 'statement', 
				'caption' => 'Statement (5.5 in. x 8.5 in.)', 
				'width' => 139.7, 
				'height' => 215.9
			]), 
			'com.mysql.wb.papertype.a3' => new \Mwb\Grt\App\PaperType([
				'name' => 'iso-a3', 
				'caption' => 'A3 (297 mm x 420 mm)', 
				'width' => 297, 
				'height' => 420.2
			]), 
			'com.mysql.wb.papertype.a2' => new \Mwb\Grt\App\PaperType([
				'name' => 'iso-a2', 
				'caption' => 'A2 (420 mm x 594 mm)', 
				'width' => 420, 
				'height' => 594
			]), 
			'com.mysql.wb.papertype.a1' => new \Mwb\Grt\App\PaperType([
				'name' => 'iso-a1', 
				'caption' => 'A1 (594 mm x 841 mm)', 
				'width' => 594, 
				'height' => 841.2
			]), 
			'com.mysql.wb.papertype.a0' => new \Mwb\Grt\App\PaperType([
				'name' => 'iso-a0', 
				'caption' => 'A0 (841 mm x 1189 mm)', 
				'width' => 841, 
				'height' => 1189
			]), 
			'com.mysql.wb.papertype.roc16k' => new \Mwb\Grt\App\PaperType([
				'name' => 'ROC 16K', 
				'caption' => 'ROC 16K (196 mm x 273 mm)', 
				'width' => 196.8, 
				'height' => 273
			]), 
			'com.mysql.wb.papertype.jb5' => new \Mwb\Grt\App\PaperType([
				'name' => 'jis-b5', 
				'caption' => 'JIS B5 (182 mm x 257 mm)', 
				'width' => 182, 
				'height' => 257.2
			]), 
			'com.mysql.wb.papertype.tabloid' => new \Mwb\Grt\App\PaperType([
				'name' => 'tabloid', 
				'caption' => 'Tabloid (11 in. x 17 in.)', 
				'width' => 279.4, 
				'height' => 431.8
			]), 
			'com.mysql.wb.papertype.quarto' => new \Mwb\Grt\App\PaperType([
				'name' => 'quarto', 
				'caption' => 'Quarto (215 mm x 275 mm.)', 
				'width' => 215, 
				'height' => 275
			]), 
			'com.mysql.wb.papertype.ansic' => new \Mwb\Grt\App\PaperType([
				'name' => 'c', 
				'caption' => 'ANSI C (432 mm x 559 mm.)', 
				'width' => 432, 
				'height' => 559
			]), 
			'com.mysql.wb.papertype.ansid' => new \Mwb\Grt\App\PaperType([
				'name' => 'd', 
				'caption' => 'ANSI D (559 mm x 864 mm.)', 
				'width' => 559, 
				'height' => 864
			]), 
			'com.mysql.wb.papertype.ansie' => new \Mwb\Grt\App\PaperType([
				'name' => 'e', 
				'caption' => 'ANSI E (864 mm x 1118 mm.)', 
				'width' => 864, 
				'height' => 1118
			])
		];
		foreach ($paperTypes as $id => $paperType) {
			$this->setInstance($id, $paperType);
		}
	}

	private function com_mysql_rdbms_Load()
	{
		/** ***********************************************************
		 * generic
		 ************************************************************ */
		$rdbms_generic = new \Mwb\Grt\Db\Mgmt\Rdbms([
			'caption' => "Generic RDBMS",
			'name' => "Generic",
			'databaseObjectPackage' => "db.generic",
			'maximumIdentifierLength' => 64,
			'doesSupportCatalogs' => 1
		]);
		$this->setInstance('com.mysql.rdbms.generic', $rdbms_generic);
		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "ODBC (manually entered parameters)",
			'description' => "Pyodbc ODBC Driver",
			'driverLibraryName' => "pyodbc",
			'connectionStringTemplate' => "DRIVER=%driver%;SERVER=%hostName%;PORT=%port%;DATABASE=%schema%;UID=%userName%;PWD=%password%;%extras%",
			'files' => new \ArrayObject([
			]),
			'hostIdentifierTemplate' => "generic@DRIVER=%driver%;SERVER=%hostName%;PORT=%port%",
			'filesTarget' => ".",
			'name' => "GenericOdbc",
			'owner' => $this->getInstanceById('com.mysql.rdbms.generic')
		]);
		$this->setInstance('com.mysql.rdbms.generic.driver.odbc', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Driver:",
			'defaultValue' => "",
			'description' => "The Driver name of your ODBC driver (E.g. FreeTDS, PostgreSQL).",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "driver",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.generic.driver.odbc.param6', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Hostname:",
			'defaultValue' => "127.0.0.1",
			'description' => "Name or IP address of the server host.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 118,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "hostName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.generic.driver.odbc.param1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Port:",
			'defaultValue' => "",
			'description' => "TCP/IP port.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 46,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "port",
			'owner' => $driver,
			'paramType' => "int",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.generic.driver.odbc.param2', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Username:",
			'defaultValue' => "root",
			'description' => "Name of the user to connect with.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "userName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.generic.driver.odbc.param3', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Password:",
			'defaultValue' => "",
			'description' => "The user's password. Will be requested later if it's not set.",
			'layoutAdvanced' => 0,
			'layoutRow' => 3,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "password",
			'owner' => $driver,
			'paramType' => "keychain",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.generic.driver.odbc.param4', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Database:",
			'defaultValue' => "",
			'description' => "The database to connect to. Leave blank to select it later.",
			'layoutAdvanced' => 0,
			'layoutRow' => 4,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "schema",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.generic.driver.odbc.param5', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Additional arguments",
			'defaultValue' => "",
			'description' => "; separated list of extra arguments to use in the\nODBC connection string.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "extras",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.generic.driver.odbc.param6', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.generic.driver.odbc.param6'), 
			$this->getInstanceById('com.mysql.rdbms.generic.driver.odbc.param1'), 
			$this->getInstanceById('com.mysql.rdbms.generic.driver.odbc.param2'), 
			$this->getInstanceById('com.mysql.rdbms.generic.driver.odbc.param3'), 
			$this->getInstanceById('com.mysql.rdbms.generic.driver.odbc.param4'), 
			$this->getInstanceById('com.mysql.rdbms.generic.driver.odbc.param5'), 
			$this->getInstanceById('com.mysql.rdbms.generic.driver.odbc.param6'), 
		]);

		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "ODBC Data Source",
			'description' => "Pyodbc ODBC Driver, using DSN",
			'driverLibraryName' => "pyodbc",
			'connectionStringTemplate' => "DSN=%dsn%",
			'files' => new \ArrayObject([
			]),
			'filesTarget' => ".",
			'name' => "GenericOdbcDSN",
			'owner' => $this->getInstanceById('com.mysql.rdbms.generic')
		]);
		$this->setInstance('com.mysql.rdbms.generic.driver.odbc_dsn', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "DSN:",
			'defaultValue' => "",
			'description' => "Pre-configured ODBC data source.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueModule' => "DbGenericRE",
			'lookupValueMethod' => "getDataSourceNames",
			'name' => "dsn",
			'owner' => $driver,
			'paramType' => "enum",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.generic.driver.odbc_dsn.param1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Password:",
			'defaultValue' => "",
			'description' => "The user's password. Will be requested later if it's not set.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "password",
			'owner' => $driver,
			'paramType' => "keychain",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.generic.driver.odbc_dsn.param2', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.generic.driver.odbc_dsn.param1'), 
			$this->getInstanceById('com.mysql.rdbms.generic.driver.odbc_dsn.param2'), 
		]);

		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "ODBC (direct connection string)",
			'description' => "Pyodbc ODBC Driver",
			'driverLibraryName' => "pyodbc",
			'connectionStringTemplate' => "%connection_string%",
			'files' => new \ArrayObject([
			]),
			'hostIdentifierTemplate' => "generic@DRIVER=%driver%;SERVER=%hostName%;PORT=%port%",
			'filesTarget' => ".",
			'name' => "GenericOdbc",
			'owner' => $this->getInstanceById('com.mysql.rdbms.generic')
		]);
		$this->setInstance('com.mysql.rdbms.generic.driver.odbc_connstr', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Connection String:",
			'defaultValue' => "",
			'description' => "The full ODBC connection string to use for the connection.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "connection_string",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.generic.driver.odbc_connstr.param1', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.generic.driver.odbc_connstr.param1'), 
		]);

		$rdbms_generic->defaultDriver = $this->getInstanceById('com.mysql.rdbms.generic.driver.odbc');


		/** ***********************************************************
		 * mssql
		 ************************************************************ */
		$rdbms_mssql = new \Mwb\Grt\Db\Mgmt\Rdbms([
			'caption' => "Microsoft SQL Server",
			'name' => "Mssql",
			'databaseObjectPackage' => "db.mssql",
			'maximumIdentifierLength' => 64,
			'doesSupportCatalogs' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mssql', $rdbms_mssql);
		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "ODBC Data Source",
			'description' => "ODBC Driver, using DSN",
			'driverLibraryName' => "pyodbc",
			'connectionStringTemplate' => "DSN=%dsn%;DATABASE=%schema%;UID=%userName%;PWD={%password%}",
			'files' => new \ArrayObject([
			]),
			'hostIdentifierTemplate' => "Mssql@%dsn%",
			'filesTarget' => ".",
			'name' => "MssqlOdbcDSN",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc_dsn', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "DSN:",
			'defaultValue' => "",
			'description' => "Pre-configured ODBC data source.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueModule' => "DbMssqlRE",
			'lookupValueMethod' => "getDataSourceNames",
			'name' => "dsn",
			'owner' => $driver,
			'paramType' => "enum",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc_dsn.param1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Username:",
			'defaultValue' => "sa",
			'description' => "Leave blank if specified in data source.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "userName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc_dsn.param2', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Password:",
			'defaultValue' => "",
			'description' => "The user's password. Leave blank to input when needed.",
			'layoutAdvanced' => 0,
			'layoutRow' => 3,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "password",
			'owner' => $driver,
			'paramType' => "keychain",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc_dsn.param3', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Database:",
			'defaultValue' => "",
			'description' => "Leave blank if already in data source or to select it later.",
			'layoutAdvanced' => 0,
			'layoutRow' => 4,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "schema",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc_dsn.param4', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc_dsn.param1'), 
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc_dsn.param2'), 
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc_dsn.param3'), 
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc_dsn.param4'), 
		]);

		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "ODBC Data Source (FreeTDS)",
			'description' => "Pyodbc using FreeTDS Driver DSN",
			'driverLibraryName' => "pyodbc",
			'connectionStringTemplate' => "DSN=%dsn%;DATABASE=%schema%;UID=%userName%;TDS_VERSION=%tdsversion%;PWD={%password%}",
			'files' => new \ArrayObject([
			]),
			'hostIdentifierTemplate' => "Mssql@%dsn%",
			'filesTarget' => ".",
			'name' => "MssqlOdbcDSNFreeTDS",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc_ftds_dsn', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "DSN:",
			'defaultValue' => "",
			'description' => "Pre-configured ODBC data source.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueModule' => "DbMssqlRE",
			'lookupValueMethod' => "getDataSourceNames",
			'name' => "dsn",
			'owner' => $driver,
			'paramType' => "enum",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc_ftds_dsn.param1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Username:",
			'defaultValue' => "sa",
			'description' => "Leave blank if specified in data source.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "userName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc_ftds_dsn.param2', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Password:",
			'defaultValue' => "",
			'description' => "The user's password. Leave blank to input when needed.",
			'layoutAdvanced' => 0,
			'layoutRow' => 3,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "password",
			'owner' => $driver,
			'paramType' => "keychain",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc_ftds_dsn.param3', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Protocol Version:",
			'defaultValue' => "7.1",
			'description' => "TDS protocol version to use by the FreeTDS driver.",
			'layoutAdvanced' => 0,
			'layoutRow' => 5,
			'layoutWidth' => 218,
			'lookupValueMethod' => "getTDSProtocolVersionChoices",
			'lookupValueModule' => "DbMssqlRE",
			'name' => "tdsversion",
			'owner' => $driver,
			'paramType' => "enum",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver. odbc_ftds_dsn.param4', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Database:",
			'defaultValue' => "",
			'description' => "Leave blank if already in data source or to select it later.",
			'layoutAdvanced' => 0,
			'layoutRow' => 4,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "schema",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc_dsn.param5', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Driver sends Unicode data as UTF-8",
			'defaultValue' => "0",
			'description' => "Enable if ODBC driver returns Unicode data as UTF-8 instead of UCS-2.\nRequired for FreeTDS.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "ODBCDriverUsesUTF8",
			'owner' => $driver,
			'paramType' => "boolean",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver. odbc_ftds_dsn.aparam2', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc_ftds_dsn.param1'), 
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc_ftds_dsn.param2'), 
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc_ftds_dsn.param3'), 
			$this->getInstanceById('com.mysql.rdbms.mssql.driver. odbc_ftds_dsn.param4'), 
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc_dsn.param5'), 
			$this->getInstanceById('com.mysql.rdbms.mssql.driver. odbc_ftds_dsn.aparam2'), 
		]);

		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "ODBC (native)",
			'description' => "Use the Native Microsoft SQL Server ODBC driver",
			'driverLibraryName' => "pyodbc",
			'connectionStringTemplate' => "DRIVER={%driver%};SERVER=%server%;DATABASE={%schema%};UID=%userName%;PWD={%password%};%extras%",
			'files' => new \ArrayObject([
			]),
			'hostIdentifierTemplate' => "Mssql@DRIVER=%driver%;SERVER=%server%",
			'filesTarget' => ".",
			'name' => "MssqlOdbcNative",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc_native', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Driver:",
			'defaultValue' => "SQL Server",
			'description' => "The name of the ODBC driver you are using.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "driver",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc_native.param0', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Server:",
			'defaultValue' => "localhost\SQLEXPRESS",
			'description' => "Address\instance name of the server.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "server",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc_native.param1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Username:",
			'defaultValue' => "sa",
			'description' => "Name of the user to connect with.",
			'layoutAdvanced' => 0,
			'layoutRow' => 3,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "userName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc_native.param3', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Password:",
			'defaultValue' => "",
			'description' => "The user's password. Leave blank to input when needed.",
			'layoutAdvanced' => 0,
			'layoutRow' => 4,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "password",
			'owner' => $driver,
			'paramType' => "keychain",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc_native.param4', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Database:",
			'defaultValue' => "",
			'description' => "The database to connect to. Leave blank to select it later.",
			'layoutAdvanced' => 0,
			'layoutRow' => 5,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "schema",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc_native.param5', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Additional arguments",
			'defaultValue' => "",
			'description' => "; separated list of extra arguments to use in the\nODBC connection string.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "extras",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc_native.aparam2', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc_native.param0'), 
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc_native.param1'), 
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc_native.param3'), 
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc_native.param4'), 
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc_native.param5'), 
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc_native.aparam2'), 
		]);

		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "ODBC (FreeTDS)",
			'description' => "Use an ODBC driver to connect to Microsoft SQL Server",
			'driverLibraryName' => "pyodbc",
			'connectionStringTemplate' => "DRIVER={%driver%};SERVER=%hostName%;PORT=%port%;DATABASE=%schema%;UID=%userName%;PWD={%password%};%extras%",
			'files' => new \ArrayObject([
			]),
			'hostIdentifierTemplate' => "Mssql@DRIVER=%driver%;SERVER=%hostName%;PORT=%port%",
			'filesTarget' => ".",
			'name' => "MssqlOdbc",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Driver:",
			'defaultValue' => "FreeTDS",
			'description' => "The name of the ODBC driver you are using (E.g. FreeTDS).",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "driver",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc.param0', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Hostname:",
			'defaultValue' => "127.0.0.1",
			'description' => "Name or IP address of the server host.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 118,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "hostName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc.param1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Port:",
			'defaultValue' => "1433",
			'description' => "TCP/IP port.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 46,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "port",
			'owner' => $driver,
			'paramType' => "int",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc.param2', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Username:",
			'defaultValue' => "sa",
			'description' => "Name of the user to connect with.",
			'layoutAdvanced' => 0,
			'layoutRow' => 3,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "userName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc.param3', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Password:",
			'defaultValue' => "",
			'description' => "The user's password. Leave blank to input when needed.",
			'layoutAdvanced' => 0,
			'layoutRow' => 4,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "password",
			'owner' => $driver,
			'paramType' => "keychain",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc.param4', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Database:",
			'defaultValue' => "",
			'description' => "The database to connect to. Leave blank to select it later.",
			'layoutAdvanced' => 0,
			'layoutRow' => 5,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "schema",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc.param5', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Driver sends Unicode data as UTF-8",
			'defaultValue' => "0",
			'description' => "Enable if ODBC driver returns Unicode data as UTF-8\ninstead of UCS-2. Required for FreeTDS.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "ODBCDriverUsesUTF8",
			'owner' => $driver,
			'paramType' => "boolean",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc.aparam1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Additional arguments",
			'defaultValue' => "TDS_VERSION=7.1",
			'description' => "; separated list of extra arguments to use in the\nODBC connection string.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "extras",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc.aparam2', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc.param0'), 
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc.param1'), 
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc.param2'), 
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc.param3'), 
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc.param4'), 
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc.param5'), 
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc.aparam1'), 
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc.aparam2'), 
		]);

		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "ODBC (connection string)",
			'description' => "Provide a full ODBC connection string for Microsoft SQL Server",
			'driverLibraryName' => "pyodbc",
			'connectionStringTemplate' => "%connstring%",
			'files' => new \ArrayObject([
			]),
			'hostIdentifierTemplate' => "Mssql@%connstring%",
			'filesTarget' => ".",
			'name' => "MssqlOdbcConnString",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc_connstring', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Connection String:",
			'defaultValue' => "",
			'description' => "The full ODBC connection string.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "connstring",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mssql.driver.odbc_connstring.param0', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc_connstring.param0'), 
		]);

		$rdbms_mssql->defaultDriver = $this->getInstanceById('com.mysql.rdbms.mssql.driver.odbc_dsn');


		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "DECIMAL",
			'numericPrecision' => "38",
			'numericPrecisionRadix' => "0",
			'numericScale' => "38",
			'parameterFormatType' => "4",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.decimal', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "NUMERIC",
			'numericPrecision' => "38",
			'numericPrecisionRadix' => "0",
			'numericScale' => "38",
			'parameterFormatType' => "4",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.numeric', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "FLOAT",
			'numericPrecision' => "15",
			'numericPrecisionRadix' => "0",
			'numericScale' => "15",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.float', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "REAL",
			'numericPrecision' => "24",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.real', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject(['UNSIGNED']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "TINYINT",
			'numericPrecision' => "3",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.tinyint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "SMALLINT",
			'numericPrecision' => "5",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.smallint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "INT",
			'numericPrecision' => "10",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.int', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "BIGINT",
			'numericPrecision' => "19",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.bigint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "8000",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "CHAR",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.char', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "8000",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "VARCHAR",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.varchar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "4000",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "NCHAR",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.nchar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "4000",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "NVARCHAR",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.nvarchar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "2147483647",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "TEXT",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.text', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "1073741823",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "NTEXT",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.ntext', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "2147483647",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.blob'),
			'name' => "IMAGE",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'needsQuotes' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.image', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "8000",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.blob'),
			'name' => "BINARY",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "2",
			'needsQuotes' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.binary', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "8000",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.blob'),
			'name' => "VARBINARY",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "2",
			'needsQuotes' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.varbinary', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "8",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "DATETIME",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.datetime', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "6",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "SMALLDATETIME",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.smalldatetime', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "8",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "TIMESTAMP",
			'needsQuotes' => "1",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.timestamp', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "8",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "DATETIME2",
			'needsQuotes' => "1",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.datetime2', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "10",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "DATETIMEOFFSET",
			'needsQuotes' => "1",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.datetimeoffset', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "3",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "DATE",
			'needsQuotes' => "1",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.date', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "5",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "TIME",
			'needsQuotes' => "1",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.time', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "BIT",
			'numericPrecision' => "1",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.bit', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "MONEY",
			'numericPrecision' => "19",
			'numericPrecisionRadix' => "0",
			'numericScale' => "4",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.money', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "SMALLMONEY",
			'numericPrecision' => "10",
			'numericPrecisionRadix' => "0",
			'numericScale' => "4",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.smallmoney', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "16",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "UNIQUEIDENTIFIER",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.uniqueidentifier', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "2147483647",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "XML",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.xml', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "160",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "SYSNAME",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.sysname', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "8",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "ROWVERSION",
			'needsQuotes' => "1",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.rowversion', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "999",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "GEOMETRY",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.geometry', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "999",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "GEOGRAPHY",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.geography', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "999",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "HIERARCHYID",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.hierarchyid', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "999",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "SQL_VARIANT",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mssql')
		]);
		$this->setInstance('com.mysql.rdbms.mssql.datatype.sqlvariant', $simpleDatatype);

		/** ***********************************************************
		 * mysql
		 ************************************************************ */
		$rdbms_mysql = new \Mwb\Grt\Db\Mgmt\Rdbms([
			'caption' => "MySQL",
			'name' => "Mysql",
			'databaseObjectPackage' => "db.mysql",
			'maximumIdentifierLength' => 64,
			'doesSupportCatalogs' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql', $rdbms_mysql);
		$driver = new \Mwb\Grt\Db\Mgmt\Driver([
			'caption' => "Standard (TCP/IP)",
			'description' => "MySQL native driver using the MySQL client library",
			'files' => new \ArrayObject([
				'libmysql.dll',
			]),
			'hostIdentifierTemplate' => "Mysql@%hostName%:%port%",
			'filesTarget' => ".",
			'driverLibraryName' => "mysqlcppconn",
			'name' => "MysqlNative",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Hostname:",
			'defaultValue' => "127.0.0.1",
			'description' => "Name or IP address of the server host",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 118,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "hostName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native.param1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Port:",
			'defaultValue' => "3306",
			'description' => "and TCP/IP port.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 46,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "port",
			'owner' => $driver,
			'paramType' => "int",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native.param2', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Username:",
			'defaultValue' => "root",
			'description' => "Name of the user to connect with.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "userName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native.param3', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Password:",
			'defaultValue' => "",
			'description' => "The user's password. Will be requested later if it's not set.",
			'layoutAdvanced' => 0,
			'layoutRow' => 3,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "password",
			'owner' => $driver,
			'paramType' => "keychain",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native.param4', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Default Schema:",
			'defaultValue' => "",
			'description' => "The schema to use as default schema. Leave blank to select it later.",
			'layoutAdvanced' => 0,
			'layoutRow' => 4,
			'layoutWidth' => 218,
			'lookupValueMethod' => "getSchemata",
			'lookupValueModule' => "ReverseEngineeringMysql",
			'name' => "schema",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native.param5', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Use compression protocol",
			'defaultValue' => "",
			'description' => "Select this option for WAN connections.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "CLIENT_COMPRESS",
			'owner' => $driver,
			'paramType' => "boolean",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native.param6', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Use ANSI quotes to quote identifiers",
			'defaultValue' => "",
			'description' => "If enabled this option overwrites the serverside settings.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "useAnsiQuotes",
			'owner' => $driver,
			'paramType' => "tristate",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native.param8', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Enable Cleartext Authentication Plugin",
			'defaultValue' => "",
			'description' => "Send user password in cleartext. Required for some authentication methods.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "OPT_ENABLE_CLEARTEXT_PLUGIN",
			'owner' => $driver,
			'paramType' => "boolean",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native.param9', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "SQL_MODE:",
			'defaultValue' => "",
			'description' => "Override the default SQL_MODE used by the server.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 180,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "SQL_MODE",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native.sqlmode', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Others:",
			'defaultValue' => "",
			'description' => "Other options for Connector/C++ as option=value pairs, one per line.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 180,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "\$others",
			'owner' => $driver,
			'paramType' => "text",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native.others', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Use SSL",
			'defaultValue' => "1",
			'description' => "Turns on SSL encryption. Connection will fail if SSL is not available.",
			'layoutAdvanced' => 2,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "useSSL",
			'owner' => $driver,
			'paramType' => "enum",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native.param7', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "SSL Key File:",
			'defaultValue' => "",
			'description' => "Path to Client Key file for SSL.",
			'layoutAdvanced' => 2,
			'layoutRow' => -1,
			'layoutWidth' => 180,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "sslKey",
			'owner' => $driver,
			'paramType' => "file",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native.ssl3', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "SSL CERT File:",
			'defaultValue' => "",
			'description' => "Path to Client Certificate file for SSL.",
			'layoutAdvanced' => 2,
			'layoutRow' => -1,
			'layoutWidth' => 180,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "sslCert",
			'owner' => $driver,
			'paramType' => "file",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native.ssl2', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "SSL CA File:",
			'defaultValue' => "",
			'description' => "Path to Certificate Authority file for SSL.",
			'layoutAdvanced' => 2,
			'layoutRow' => -1,
			'layoutWidth' => 180,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "sslCA",
			'owner' => $driver,
			'paramType' => "file",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native.ssl1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "SSL Cipher:",
			'defaultValue' => "",
			'description' => "Optional : separated list of permissible ciphers to use for SSL encryption.",
			'layoutAdvanced' => 2,
			'layoutRow' => -1,
			'layoutWidth' => 180,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "sslCipher",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native.ssl4', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "SSL Wizard ...",
			'defaultValue' => "",
			'description' => "",
			'layoutAdvanced' => 2,
			'layoutRow' => -1,
			'layoutWidth' => 120,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "sslWizard",
			'owner' => $driver,
			'paramType' => "button",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native.ssl5', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Files ...",
			'defaultValue' => "",
			'description' => "",
			'layoutAdvanced' => 2,
			'layoutRow' => -1,
			'layoutWidth' => 120,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "openSSLWizardDirectory",
			'owner' => $driver,
			'paramType' => "button",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native.ssl6', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native.param1'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native.param2'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native.param3'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native.param4'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native.param5'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native.param6'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native.param8'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native.param9'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native.sqlmode'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native.others'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native.param7'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native.ssl3'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native.ssl2'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native.ssl1'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native.ssl4'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native.ssl5'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native.ssl6'), 
		]);

		$driver = new \Mwb\Grt\Db\Mgmt\Driver([
			'caption' => "Local Socket/Pipe",
			'description' => "MySQL native driver using the MySQL client library",
			'files' => new \ArrayObject([
				'libmysql.dll',
			]),
			'hostIdentifierTemplate' => "Mysql@local:%socket%",
			'filesTarget' => ".",
			'driverLibraryName' => "mysqlcppconn",
			'name' => "MysqlNativeSocket",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_socket', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Socket/Pipe Path:",
			'defaultValue' => "",
			'description' => "Path to local socket or pipe file. Leave empty for default.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "socket",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_socket.param1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Username:",
			'defaultValue' => "root",
			'description' => "Name of the user to connect with.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "userName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_socket.param3', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Password:",
			'defaultValue' => "",
			'description' => "The user's password. Will be requested later if it's not set now.",
			'layoutAdvanced' => 0,
			'layoutRow' => 3,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "password",
			'owner' => $driver,
			'paramType' => "keychain",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_socket.param4', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Default Schema:",
			'defaultValue' => "",
			'description' => "The schema to use as default schema. Leave blank to select it later.",
			'layoutAdvanced' => 0,
			'layoutRow' => 4,
			'layoutWidth' => 218,
			'lookupValueMethod' => "getSchemata",
			'lookupValueModule' => "ReverseEngineeringMysql",
			'name' => "schema",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_socket.param5', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Use ANSI quotes to quote identifiers",
			'defaultValue' => "",
			'description' => "If enabled this option overwrites the serverside settings.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "useAnsiQuotes",
			'owner' => $driver,
			'paramType' => "tristate",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_socket.param8', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Enable Cleartext Authentication Plugin",
			'defaultValue' => "",
			'description' => "Send user password in cleartext. Required for some authentication methods.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "OPT_ENABLE_CLEARTEXT_PLUGIN",
			'owner' => $driver,
			'paramType' => "boolean",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_socket.param9', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "SQL_MODE:",
			'defaultValue' => "",
			'description' => "Override the default SQL_MODE used by the server.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 180,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "SQL_MODE",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_socket.sqlmode', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Others:",
			'defaultValue' => "",
			'description' => "Other options for Connector/C++ as option=value pairs, one per line",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 180,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "\$others",
			'owner' => $driver,
			'paramType' => "text",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_socket.others', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Use SSL",
			'defaultValue' => "1",
			'description' => "Turns on SSL encryption. Connection will fail if SSL is not available.",
			'layoutAdvanced' => 2,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "useSSL",
			'owner' => $driver,
			'paramType' => "enum",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_socket.ssl0', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "SSL CA File:",
			'defaultValue' => "",
			'description' => "Path to Certificate Authority file for SSL.",
			'layoutAdvanced' => 2,
			'layoutRow' => -1,
			'layoutWidth' => 180,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "sslCA",
			'owner' => $driver,
			'paramType' => "file",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_socket.ssl1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "SSL CERT File:",
			'defaultValue' => "",
			'description' => "Path to Client Certificate file for SSL.",
			'layoutAdvanced' => 2,
			'layoutRow' => -1,
			'layoutWidth' => 180,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "sslCert",
			'owner' => $driver,
			'paramType' => "file",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_socket.ssl2', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "SSL Key File:",
			'defaultValue' => "",
			'description' => "Path to Client Key file for SSL.",
			'layoutAdvanced' => 2,
			'layoutRow' => -1,
			'layoutWidth' => 180,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "sslKey",
			'owner' => $driver,
			'paramType' => "file",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_socket.ssl3', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "SSL Cipher:",
			'defaultValue' => "",
			'description' => "Optional : separated list of permissible ciphers to use for SSL encryption.",
			'layoutAdvanced' => 2,
			'layoutRow' => -1,
			'layoutWidth' => 180,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "sslCipher",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_socket.ssl4', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "SSL Wizard ...",
			'defaultValue' => "",
			'description' => "",
			'layoutAdvanced' => 2,
			'layoutRow' => -1,
			'layoutWidth' => 120,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "sslWizard",
			'owner' => $driver,
			'paramType' => "button",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native.ssl5', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Files ...",
			'defaultValue' => "",
			'description' => "",
			'layoutAdvanced' => 2,
			'layoutRow' => -1,
			'layoutWidth' => 120,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "openSSLWizardDirectory",
			'owner' => $driver,
			'paramType' => "button",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native.ssl6', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_socket.param1'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_socket.param3'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_socket.param4'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_socket.param5'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_socket.param8'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_socket.param9'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_socket.sqlmode'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_socket.others'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_socket.ssl0'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_socket.ssl1'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_socket.ssl2'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_socket.ssl3'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_socket.ssl4'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native.ssl5'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native.ssl6'), 
		]);

		$driver = new \Mwb\Grt\Db\Mgmt\Driver([
			'caption' => "Standard TCP/IP over SSH",
			'description' => "MySQL native driver using the MySQL client library",
			'files' => new \ArrayObject([
				'libmysql.dll',
			]),
			'hostIdentifierTemplate' => "Mysql@%hostName%:%port%@%sshHost%",
			'filesTarget' => ".",
			'driverLibraryName' => "mysqlcppconn",
			'name' => "MysqlNativeSSH",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_sshtun', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "SSH Hostname:",
			'defaultValue' => "127.0.0.1:22",
			'description' => "SSH server hostname, with  optional port number.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "sshHost",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_sshtun.sparam1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "SSH Username:",
			'defaultValue' => "user",
			'description' => "Name of the SSH user to connect with.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "sshUserName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_sshtun.sparam3', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "SSH Password:",
			'defaultValue' => "",
			'description' => "SSH user password to connect to the SSH tunnel.",
			'layoutAdvanced' => 0,
			'layoutRow' => 3,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "sshPassword",
			'owner' => $driver,
			'paramType' => "keychain",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_sshtun.sparam4', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "SSH Key File:",
			'defaultValue' => "",
			'description' => "Path to SSH private key file.",
			'layoutAdvanced' => 0,
			'layoutRow' => 4,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "sshKeyFile",
			'owner' => $driver,
			'paramType' => "file",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_sshtun.sparam5', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "MySQL Hostname:",
			'defaultValue' => "127.0.0.1",
			'description' => "MySQL server host relative to the SSH server.",
			'layoutAdvanced' => 0,
			'layoutRow' => 6,
			'layoutWidth' => 118,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "hostName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_sshtun.param1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "MySQL Server Port:",
			'defaultValue' => "3306",
			'description' => "TCP/IP port of the MySQL server.",
			'layoutAdvanced' => 0,
			'layoutRow' => 7,
			'layoutWidth' => 46,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "port",
			'owner' => $driver,
			'paramType' => "int",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_sshtun.param2', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Username:",
			'defaultValue' => "root",
			'description' => "Name of the user to connect with.",
			'layoutAdvanced' => 0,
			'layoutRow' => 8,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "userName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_sshtun.param3', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Password:",
			'defaultValue' => "",
			'description' => "The MySQL user's password. Will be requested later if not set.",
			'layoutAdvanced' => 0,
			'layoutRow' => 9,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "password",
			'owner' => $driver,
			'paramType' => "keychain",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_sshtun.param4', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Default Schema:",
			'defaultValue' => "",
			'description' => "The schema to use as default schema. Leave blank to select it later.",
			'layoutAdvanced' => 0,
			'layoutRow' => 10,
			'layoutWidth' => 218,
			'lookupValueMethod' => "getSchemata",
			'lookupValueModule' => "ReverseEngineeringMysql",
			'name' => "schema",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_sshtun.param5', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Use compression protocol",
			'defaultValue' => "",
			'description' => "Select this option for WAN connections.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "CLIENT_COMPRESS",
			'owner' => $driver,
			'paramType' => "boolean",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_sshtun.aparam1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Use ANSI quotes to quote identifiers",
			'defaultValue' => "",
			'description' => "If enabled this option overwrites the serverside settings.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "useAnsiQuotes",
			'owner' => $driver,
			'paramType' => "tristate",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_sshtun.aparam3', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Enable Cleartext Authentication Plugin",
			'defaultValue' => "",
			'description' => "Send user password in cleartext. Required for some authentication methods.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "OPT_ENABLE_CLEARTEXT_PLUGIN",
			'owner' => $driver,
			'paramType' => "boolean",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_sshtun.aparam4', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "SQL_MODE:",
			'defaultValue' => "",
			'description' => "Override the default SQL_MODE used by the server.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 180,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "SQL_MODE",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_sshtun.sqlmode', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Others:",
			'defaultValue' => "",
			'description' => "Other options for Connector/C++ as option=value pairs, one per line.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 180,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "\$others",
			'owner' => $driver,
			'paramType' => "text",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_sshtun.others', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Use SSL",
			'defaultValue' => "1",
			'description' => "Turns on SSL encryption. Connection will fail if SSL is not available.",
			'layoutAdvanced' => 2,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "useSSL",
			'owner' => $driver,
			'paramType' => "enum",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_sshtun.aparam2', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "SSL CA File:",
			'defaultValue' => "",
			'description' => "Path to Certificate Authority file for SSL.",
			'layoutAdvanced' => 2,
			'layoutRow' => -1,
			'layoutWidth' => 180,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "sslCA",
			'owner' => $driver,
			'paramType' => "file",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_sshtun.ssl1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "SSL CERT File:",
			'defaultValue' => "",
			'description' => "Path to Client Certificate file for SSL.",
			'layoutAdvanced' => 2,
			'layoutRow' => -1,
			'layoutWidth' => 180,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "sslCert",
			'owner' => $driver,
			'paramType' => "file",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_sshtun.ssl2', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "SSL Key File:",
			'defaultValue' => "",
			'description' => "Path to Client Key file for SSL.",
			'layoutAdvanced' => 2,
			'layoutRow' => -1,
			'layoutWidth' => 180,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "sslKey",
			'owner' => $driver,
			'paramType' => "file",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_sshtun.ssl3', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "SSL Cipher:",
			'defaultValue' => "",
			'description' => "Optional : separated list of permissible ciphers to use for SSL encryption.",
			'layoutAdvanced' => 2,
			'layoutRow' => -1,
			'layoutWidth' => 180,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "sslCipher",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native_sshtun.ssl4', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "SSL Wizard ...",
			'defaultValue' => "",
			'description' => "",
			'layoutAdvanced' => 2,
			'layoutRow' => -1,
			'layoutWidth' => 120,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "sslWizard",
			'owner' => $driver,
			'paramType' => "button",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native.ssl5', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Files ...",
			'defaultValue' => "",
			'description' => "",
			'layoutAdvanced' => 2,
			'layoutRow' => -1,
			'layoutWidth' => 120,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "openSSLWizardDirectory",
			'owner' => $driver,
			'paramType' => "button",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.native.ssl6', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_sshtun.sparam1'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_sshtun.sparam3'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_sshtun.sparam4'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_sshtun.sparam5'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_sshtun.param1'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_sshtun.param2'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_sshtun.param3'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_sshtun.param4'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_sshtun.param5'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_sshtun.aparam1'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_sshtun.aparam3'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_sshtun.aparam4'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_sshtun.sqlmode'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_sshtun.others'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_sshtun.aparam2'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_sshtun.ssl1'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_sshtun.ssl2'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_sshtun.ssl3'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native_sshtun.ssl4'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native.ssl5'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.native.ssl6'), 
		]);

		$driver = new \Mwb\Grt\Db\Mgmt\Driver([
			'caption' => "MySQL Fabric Management Node",
			'description' => "Connection using the MySQL Fabric Utility",
			'hostIdentifierTemplate' => "Mysql@%hostName%:%port%",
			'filesTarget' => ".",
			'driverLibraryName' => "mysqlcppconn",
			'name' => "MySQLFabric",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.fabric', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Hostname:",
			'defaultValue' => "127.0.0.1",
			'description' => "Name or IP address of the fabric node",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 118,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "hostName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.fabric.param1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Port:",
			'defaultValue' => "32275",
			'description' => "and TCP/IP port.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 46,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "port",
			'owner' => $driver,
			'paramType' => "int",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.fabric.param2', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Username:",
			'defaultValue' => "admin",
			'description' => "Name of the user to connect with.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "userName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.fabric.param3', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Password:",
			'defaultValue' => "",
			'description' => "The user's password. Will be requested later  if it's not set.",
			'layoutAdvanced' => 0,
			'layoutRow' => 3,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "password",
			'owner' => $driver,
			'paramType' => "keychain",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.fabric.param4', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "MySQL Username:",
			'defaultValue' => "fabric",
			'description' => "MySQL account to use when connecting to managed MySQL Instances.",
			'layoutAdvanced' => 3,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "mysqlUserName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.fabric.param5', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "H.A. Group Filter:",
			'defaultValue' => "",
			'description' => "Comma separated list of groups to be included. Leave empty to include all the available groups.",
			'layoutAdvanced' => 3,
			'layoutRow' => 2,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "haGroupFilter",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.fabric.param6', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'defaultValue' => "0",
			'layoutAdvanced' => -1,
			'layoutRow' => -1,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "connections_created",
			'owner' => $driver,
			'paramType' => "int",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.fabric.param7', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'defaultValue' => "0000-00-00 00:00:00",
			'layoutAdvanced' => -1,
			'layoutRow' => -1,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "managedConnectionsUpdateTime",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.mysql.driver.fabric.param8', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.fabric.param1'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.fabric.param2'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.fabric.param3'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.fabric.param4'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.fabric.param5'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.fabric.param6'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.fabric.param7'), 
			$this->getInstanceById('com.mysql.rdbms.mysql.driver.fabric.param8'), 
		]);

		$rdbms_mysql->defaultDriver = $this->getInstanceById('com.mysql.rdbms.mysql.driver.native');


		$privilege = new \Mwb\Grt\Db\Mgmt\PrivilegeMapping([
			'structName' => "db.mysql.Schema",
			'privileges' => new \ArrayObject([
				'ALL',
				'CREATE',
				'DROP',
				'GRANT OPTION',
				'REFERENCES',
				'EVENT',
				'LOCK TABLES',
			])
		]);
		$this->setInstance('com.mysql.rdbms.mysql.privileges.schema', $privilege);

		$privilege = new \Mwb\Grt\Db\Mgmt\PrivilegeMapping([
			'structName' => "db.mysql.Table",
			'privileges' => new \ArrayObject([
				'ALL',
				'CREATE',
				'DROP',
				'GRANT OPTION',
				'REFERENCES',
				'ALTER',
				'DELETE',
				'INDEX',
				'INSERT',
				'SELECT',
				'UPDATE',
				'TRIGGER',
			])
		]);
		$this->setInstance('com.mysql.rdbms.mysql.privileges.table', $privilege);

		$privilege = new \Mwb\Grt\Db\Mgmt\PrivilegeMapping([
			'structName' => "db.mysql.View",
			'privileges' => new \ArrayObject([
				'ALL',
				'CREATE VIEW',
				'SHOW VIEW',
				'DROP',
				'REFERENCES',
				'ALTER',
				'DELETE',
				'INDEX',
				'INSERT',
				'SELECT',
				'UPDATE',
				'TRIGGER',
			])
		]);
		$this->setInstance('com.mysql.rdbms.mysql.privileges.view', $privilege);

		$privilege = new \Mwb\Grt\Db\Mgmt\PrivilegeMapping([
			'structName' => "db.mysql.Routine",
			'privileges' => new \ArrayObject([
				'ALL',
				'ALTER ROUTINE',
				'CREATE ROUTINE',
				'EXECUTE',
			])
		]);
		$this->setInstance('com.mysql.rdbms.mysql.privileges.routine', $privilege);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['big5_chinese_ci', 'big5_bin']),
			'defaultCollation' => "big5_chinese_ci",
			'description' => "Big5 Traditional Chinese",
			'name' => "big5",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.big5', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['dec8_swedish_ci', 'dec8_bin']),
			'defaultCollation' => "dec8_swedish_ci",
			'description' => "DEC West European",
			'name' => "dec8",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.dec8', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['cp850_general_ci', 'cp850_bin']),
			'defaultCollation' => "cp850_general_ci",
			'description' => "DOS West European",
			'name' => "cp850",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.cp850', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['hp8_english_ci', 'hp8_bin']),
			'defaultCollation' => "hp8_english_ci",
			'description' => "HP West European",
			'name' => "hp8",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.hp8', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['koi8r_general_ci', 'koi8r_bin']),
			'defaultCollation' => "koi8r_general_ci",
			'description' => "KOI8-R Relcom Russian",
			'name' => "koi8r",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.koi8r', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['latin1_german1_ci', 'latin1_swedish_ci', 'latin1_danish_ci', 'latin1_german2_ci', 'latin1_bin', 'latin1_general_ci', 'latin1_general_cs', 'latin1_spanish_ci']),
			'defaultCollation' => "latin1_swedish_ci",
			'description' => "ISO 8859-1 West European",
			'name' => "latin1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.latin1', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['latin2_czech_cs', 'latin2_general_ci', 'latin2_hungarian_ci', 'latin2_croatian_ci', 'latin2_bin']),
			'defaultCollation' => "latin2_general_ci",
			'description' => "ISO 8859-2 Central European",
			'name' => "latin2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.latin2', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['swe7_swedish_ci', 'swe7_bin']),
			'defaultCollation' => "swe7_swedish_ci",
			'description' => "7bit Swedish",
			'name' => "swe7",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.swe7', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['ascii_general_ci', 'ascii_bin']),
			'defaultCollation' => "ascii_general_ci",
			'description' => "US ASCII",
			'name' => "ascii",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.ascii', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['ujis_japanese_ci', 'ujis_bin']),
			'defaultCollation' => "ujis_japanese_ci",
			'description' => "EUC-JP Japanese",
			'name' => "ujis",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.ujis', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['sjis_japanese_ci', 'sjis_bin']),
			'defaultCollation' => "sjis_japanese_ci",
			'description' => "Shift-JIS Japanese",
			'name' => "sjis",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.sjis', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['hebrew_general_ci', 'hebrew_bin']),
			'defaultCollation' => "hebrew_general_ci",
			'description' => "ISO 8859-8 Hebrew",
			'name' => "hebrew",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.hebrew', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['tis620_thai_ci', 'tis620_bin']),
			'defaultCollation' => "tis620_thai_ci",
			'description' => "TIS620 Thai",
			'name' => "tis620",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.tis620', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['euckr_korean_ci', 'euckr_bin']),
			'defaultCollation' => "euckr_korean_ci",
			'description' => "EUC-KR Korean",
			'name' => "euckr",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.euckr', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['koi8u_general_ci', 'koi8u_bin']),
			'defaultCollation' => "koi8u_general_ci",
			'description' => "KOI8-U Ukrainian",
			'name' => "koi8u",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.koi8u', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['gb2312_chinese_ci', 'gb2312_bin']),
			'defaultCollation' => "gb2312_chinese_ci",
			'description' => "GB2312 Simplified Chinese",
			'name' => "gb2312",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.gb2312', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['greek_general_ci', 'greek_bin']),
			'defaultCollation' => "greek_general_ci",
			'description' => "ISO 8859-7 Greek",
			'name' => "greek",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.greek', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['cp1250_general_ci', 'cp1250_czech_cs', 'cp1250_croatian_ci', 'cp1250_bin']),
			'defaultCollation' => "cp1250_general_ci",
			'description' => "Windows Central European",
			'name' => "cp1250",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.cp1250', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['gbk_chinese_ci', 'gbk_bin']),
			'defaultCollation' => "gbk_chinese_ci",
			'description' => "GBK Simplified Chinese",
			'name' => "gbk",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.gbk', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['latin5_turkish_ci', 'latin5_bin']),
			'defaultCollation' => "latin5_turkish_ci",
			'description' => "ISO 8859-9 Turkish",
			'name' => "latin5",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.latin5', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['armscii8_general_ci', 'armscii8_bin']),
			'defaultCollation' => "armscii8_general_ci",
			'description' => "ARMSCII-8 Armenian",
			'name' => "armscii8",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.asmscii8', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['utf8_bin', 'utf8_czech_ci', 'utf8_danish_ci', 'utf8_esperanto_ci', 'utf8_estonian_ci', 'utf8_general_ci', 'utf8_general_mysql500_ci', 'utf8_hungarian_ci', 'utf8_icelandic_ci', 'utf8_latvian_ci', 'utf8_lithuanian_ci', 'utf8_persian_ci', 'utf8_polish_ci', 'utf8_roman_ci', 'utf8_romanian_ci', 'utf8_sinhala_ci', 'utf8_slovak_ci', 'utf8_slovenian_ci', 'utf8_spanish_ci', 'utf8_spanish2_ci', 'utf8_swedish_ci', 'utf8_turkish_ci', 'utf8_unicode_ci']),
			'defaultCollation' => "utf8_general_ci",
			'description' => "UTF-8 Unicode",
			'name' => "utf8",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.utf8', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['ucs2_bin', 'ucs2_czech_ci', 'ucs2_danish_ci', 'ucs2_esperanto_ci', 'ucs2_estonian_ci', 'ucs2_general_ci', 'ucs2_general_mysql500_ci', 'ucs2_hungarian_ci', 'ucs2_icelandic_ci', 'ucs2_latvian_ci', 'ucs2_lithuanian_ci', 'ucs2_persian_ci', 'ucs2_polish_ci', 'ucs2_roman_ci', 'ucs2_romanian_ci', 'ucs2_sinhala_ci', 'ucs2_slovak_ci', 'ucs2_slovenian_ci', 'ucs2_spanish_ci', 'ucs2_spanish2_ci', 'ucs2_swedish_ci', 'ucs2_turkish_ci', 'ucs2_unicode_ci']),
			'defaultCollation' => "ucs2_general_ci",
			'description' => "UCS-2 Unicode",
			'name' => "ucs2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.ucs2', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['cp866_general_ci', 'cp866_bin']),
			'defaultCollation' => "cp866_general_ci",
			'description' => "DOS Russian",
			'name' => "cp866",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.cp866', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['keybcs2_general_ci', 'keybcs2_bin']),
			'defaultCollation' => "keybcs2_general_ci",
			'description' => "DOS Kamenicky Czech-Slovak",
			'name' => "keybcs2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.keybcs2', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['macce_general_ci', 'macce_bin']),
			'defaultCollation' => "macce_general_ci",
			'description' => "Mac Central European",
			'name' => "macce",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.macce', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['macroman_general_ci', 'macroman_bin']),
			'defaultCollation' => "macroman_general_ci",
			'description' => "Mac West European",
			'name' => "macroman",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.macroman', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['cp852_general_ci', 'cp852_bin']),
			'defaultCollation' => "cp852_general_ci",
			'description' => "DOS Central European",
			'name' => "cp852",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.cp852', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['latin7_estonian_cs', 'latin7_general_ci', 'latin7_general_cs', 'latin7_bin']),
			'defaultCollation' => "latin7_general_ci",
			'description' => "ISO 8859-13 Baltic",
			'name' => "latin7",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.latin7', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['cp1251_bulgarian_ci', 'cp1251_ukrainian_ci', 'cp1251_bin', 'cp1251_general_ci', 'cp1251_general_cs']),
			'defaultCollation' => "cp1251_general_ci",
			'description' => "Windows Cyrillic",
			'name' => "cp1251",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.cp1251', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['cp1256_general_ci', 'cp1256_bin']),
			'defaultCollation' => "cp1256_general_ci",
			'description' => "Windows Arabic",
			'name' => "cp1256",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.cp1256', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['cp1257_lithuanian_ci', 'cp1257_bin', 'cp1257_general_ci']),
			'defaultCollation' => "cp1257_general_ci",
			'description' => "Windows Baltic",
			'name' => "cp1257",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.cp1257', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['binary']),
			'defaultCollation' => "binary",
			'description' => "Binary pseudo charset",
			'name' => "binary",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.binary', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['geostd8_general_ci', 'geostd8_bin']),
			'defaultCollation' => "geostd8_general_ci",
			'description' => "GEOSTD8 Georgian",
			'name' => "geostd8",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.geostd8', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['cp932_japanese_ci', 'cp932_bin']),
			'defaultCollation' => "cp932_japanese_ci",
			'description' => "SJIS for Windows Japanese",
			'name' => "cp932",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.cp932', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['eucjpms_japanese_ci', 'eucjpms_bin']),
			'defaultCollation' => "eucjpms_japanese_ci",
			'description' => "UJIS for Windows Japanese",
			'name' => "eucjpms",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.eucjpms', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['utf8mb4_bin', 'utf8mb4_czech_ci', 'utf8mb4_danish_ci', 'utf8mb4_esperanto_ci', 'utf8mb4_estonian_ci', 'utf8mb4_general_ci', 'utf8mb4_general_mysql500_ci', 'utf8mb4_hungarian_ci', 'utf8mb4_icelandic_ci', 'utf8mb4_latvian_ci', 'utf8mb4_lithuanian_ci', 'utf8mb4_persian_ci', 'utf8mb4_polish_ci', 'utf8mb4_roman_ci', 'utf8mb4_romanian_ci', 'utf8mb4_sinhala_ci', 'utf8mb4_slovak_ci', 'utf8mb4_slovenian_ci', 'utf8mb4_spanish_ci', 'utf8mb4_spanish2_ci', 'utf8mb4_swedish_ci', 'utf8mb4_turkish_ci', 'utf8mb4_unicode_ci']),
			'defaultCollation' => "utf8mb4_general_ci",
			'description' => "UTF-8 Unicode",
			'name' => "utf8mb4",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.utf8mb4', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['utf16_bin', 'utf16_czech_ci', 'utf16_danish_ci', 'utf16_esperanto_ci', 'utf16_estonian_ci', 'utf16_general_ci', 'utf16_general_mysql500_ci', 'utf16_hungarian_ci', 'utf16_icelandic_ci', 'utf16_latvian_ci', 'utf16_lithuanian_ci', 'utf16_persian_ci', 'utf16_polish_ci', 'utf16_roman_ci', 'utf16_romanian_ci', 'utf16_sinhala_ci', 'utf16_slovak_ci', 'utf16_slovenian_ci', 'utf16_spanish_ci', 'utf16_spanish2_ci', 'utf16_swedish_ci', 'utf16_turkish_ci', 'utf16_unicode_ci']),
			'defaultCollation' => "utf16_general_ci",
			'description' => "UTF-16 Unicode",
			'name' => "utf16",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.utf16', $characterSet);

		$characterSet = new \Mwb\Grt\Db\CharacterSet([
			'collations' => new \ArrayObject(['utf32_bin', 'utf32_czech_ci', 'utf32_danish_ci', 'utf32_esperanto_ci', 'utf32_estonian_ci', 'utf32_general_ci', 'utf32_general_mysql500_ci', 'utf32_hungarian_ci', 'utf32_icelandic_ci', 'utf32_latvian_ci', 'utf32_lithuanian_ci', 'utf32_persian_ci', 'utf32_polish_ci', 'utf32_roman_ci', 'utf32_romanian_ci', 'utf32_sinhala_ci', 'utf32_slovak_ci', 'utf32_slovenian_ci', 'utf32_spanish_ci', 'utf32_spanish2_ci', 'utf32_swedish_ci', 'utf32_turkish_ci', 'utf32_unicode_ci']),
			'defaultCollation' => "utf32_general_ci",
			'description' => "UTF-32 Unicode",
			'name' => "utf32",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.charset.utf32', $characterSet);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject(['UNSIGNED', 'ZEROFILL']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "TINYINT",
			'numericPrecision' => "3",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql'),
			'synonyms' => new \ArrayObject(['BOOL', 'BOOLEAN', 'INT1'])
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.tinyint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject(['UNSIGNED', 'ZEROFILL']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "SMALLINT",
			'numericPrecision' => "5",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql'),
			'synonyms' => new \ArrayObject(['INT2'])
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.smallint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject(['UNSIGNED', 'ZEROFILL']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "MEDIUMINT",
			'numericPrecision' => "8",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql'),
			'synonyms' => new \ArrayObject(['INT3', 'MIDDLEINT'])
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.mediumint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject(['UNSIGNED', 'ZEROFILL']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "INT",
			'numericPrecision' => "10",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql'),
			'synonyms' => new \ArrayObject(['INTEGER', 'INT4'])
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.int', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject(['UNSIGNED', 'ZEROFILL']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "BIGINT",
			'numericPrecision' => "20",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql'),
			'synonyms' => new \ArrayObject(['INT8', 'SERIAL'])
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.bigint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject(['UNSIGNED', 'ZEROFILL']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "FLOAT",
			'numericPrecision' => "53",
			'numericPrecisionRadix' => "0",
			'numericScale' => "30",
			'parameterFormatType' => "6",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql'),
			'synonyms' => new \ArrayObject(['FLOAT4'])
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.float', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject(['UNSIGNED', 'ZEROFILL']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "REAL",
			'numericPrecision' => "53",
			'numericPrecisionRadix' => "0",
			'numericScale' => "30",
			'parameterFormatType' => "5",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.real', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject(['UNSIGNED', 'ZEROFILL']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "DOUBLE",
			'numericPrecision' => "53",
			'numericPrecisionRadix' => "0",
			'numericScale' => "30",
			'parameterFormatType' => "5",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql'),
			'synonyms' => new \ArrayObject(['FLOAT8'])
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.double', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject(['UNSIGNED', 'ZEROFILL']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "DECIMAL",
			'numericPrecision' => "65",
			'numericPrecisionRadix' => "0",
			'numericScale' => "30",
			'parameterFormatType' => "6",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql'),
			'synonyms' => new \ArrayObject(['FIXED', 'NUMERIC'])
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.decimal', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "255",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject(['BINARY', 'UNICODE', 'ASCII']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "CHAR",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql'),
			'synonyms' => new \ArrayObject(['NCHAR', 'NATIONAL CHAR'])
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.char', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "255",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject(['BINARY', 'UNICODE', 'ASCII']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "CHAR",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql'),
			'synonyms' => new \ArrayObject(['NATIONAL CHAR'])
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.nchar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "65535",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject(['BINARY', 'UNICODE', 'ASCII']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "VARCHAR",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql'),
			'synonyms' => new \ArrayObject(['CHAR VARYING'])
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.varchar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "65535",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject(['BINARY', 'UNICODE', 'ASCII']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "NVARCHAR",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql'),
			'synonyms' => new \ArrayObject(['NATIONAL VARCHAR', 'NCHAR VARCHAR', 'NATIONAL CHAR VARYING', 'NCHAR VARYING'])
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.nvarchar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "255",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.blob'),
			'name' => "BINARY",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.binary', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "65535",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.blob'),
			'name' => "VARBINARY",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.varbinary', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "255",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject(['BINARY']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "TINYTEXT",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.tinytext', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "65535",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject(['BINARY', 'ASCII', 'UNICODE']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "TEXT",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.text', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-24",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject(['BINARY', 'ASCII', 'UNICODE']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "MEDIUMTEXT",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql'),
			'synonyms' => new \ArrayObject(['LONG', 'LONG VARCHAR', 'LONG CHAR VARYING'])
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.mediumtext', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-32",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject(['BINARY', 'ASCII', 'UNICODE']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "LONGTEXT",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.longtext', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.blob'),
			'name' => "TINYBLOB",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'needsQuotes' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.tinyblob', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "65535",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.blob'),
			'name' => "BLOB",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "2",
			'needsQuotes' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.blob', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.blob'),
			'name' => "MEDIUMBLOB",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'needsQuotes' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql'),
			'synonyms' => new \ArrayObject(['LONG VARBINARY'])
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.mediumblob', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.blob'),
			'name' => "LONGBLOB",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'needsQuotes' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.longblob', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'validity' => ">=5.7.7",
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "JSON",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'needsQuotes' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.json', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'validity' => "<5.6",
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "8",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "DATETIME",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.datetime', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'validity' => ">=5.6",
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "8",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "DATETIME",
			'numericPrecision' => "6",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.datetime_f', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "DATE",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.date', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'validity' => "<5.6",
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "3",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "TIME",
			'needsQuotes' => "1",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.time', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'validity' => ">=5.6",
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "3",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "TIME",
			'needsQuotes' => "1",
			'numericPrecision' => "6",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.time_f', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "YEAR",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.year', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'validity' => "<5.6",
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "4",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "TIMESTAMP",
			'needsQuotes' => "1",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.timestamp', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'validity' => ">=5.6",
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "4",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "TIMESTAMP",
			'needsQuotes' => "1",
			'numericPrecision' => "6",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.timestamp_f', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.gis'),
			'name' => "GEOMETRY",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.geometry', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.gis'),
			'name' => "POINT",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.point', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.gis'),
			'name' => "LINESTRING",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.linestring', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.gis'),
			'name' => "POLYGON",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.polygon', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.gis'),
			'name' => "GEOMETRYCOLLECTION",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.geometrycollection', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.gis'),
			'name' => "MULTIPOINT",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.multipoint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.gis'),
			'name' => "MULTILINESTRING",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.multilinestring', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.gis'),
			'name' => "MULTIPOLYGON",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.multipolygon', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "BIT",
			'numericPrecision' => "2",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.bit', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "ENUM",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "10",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.enum', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "SET",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "10",
			'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
		]);
		$this->setInstance('com.mysql.rdbms.mysql.datatype.set', $simpleDatatype);

		/** ***********************************************************
		 * postgresql
		 ************************************************************ */
		$rdbms_postgresql = new \Mwb\Grt\Db\Mgmt\Rdbms([
			'caption' => "PostgreSQL",
			'name' => "Postgresql",
			'databaseObjectPackage' => "db.postgresql",
			'maximumIdentifierLength' => 64,
			'doesSupportCatalogs' => 1
		]);
		$this->setInstance('com.mysql.rdbms.postgresql', $rdbms_postgresql);
		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "ODBC (manually entered parameters)",
			'description' => "Pyodbc ODBC Driver",
			'driverLibraryName' => "pyodbc",
			'connectionStringTemplate' => "DRIVER=%driver%;SERVER=%hostName%;PORT=%port%;DATABASE=%schema%;UID=%userName%;UseDeclareFetch=%useDeclareFetch%;%extras%",
			'files' => new \ArrayObject([
			]),
			'hostIdentifierTemplate' => "postgresql@DRIVER=%driver%;SERVER=%hostName%;PORT=%port%",
			'filesTarget' => ".",
			'name' => "PostgresqlOdbc",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.driver.odbc', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Driver:",
			'defaultValue' => "PostgreSQL ANSI",
			'description' => "The Driver name of your ODBC driver (E.g. psqlodbc, PostgreSQL ANSI).",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "driver",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.driver.odbc.param6', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Hostname:",
			'defaultValue' => "127.0.0.1",
			'description' => "Name or IP address of the server host.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 118,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "hostName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.driver.odbc.param1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Port:",
			'defaultValue' => "5432",
			'description' => "TCP/IP port.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 46,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "port",
			'owner' => $driver,
			'paramType' => "int",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.driver.odbc.param2', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Username:",
			'defaultValue' => "postgres",
			'description' => "Name of the user to connect with.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "userName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.driver.odbc.param3', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Password:",
			'defaultValue' => "",
			'description' => "The user's password. Will be requested later if it's not set.",
			'layoutAdvanced' => 0,
			'layoutRow' => 3,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "password",
			'owner' => $driver,
			'paramType' => "keychain",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.driver.odbc.param4', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Database:",
			'defaultValue' => "",
			'description' => "The database to connect to. Cannot be blank.",
			'layoutAdvanced' => 0,
			'layoutRow' => 4,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "schema",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.driver.odbc.param5', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Use Declare Fetch",
			'defaultValue' => "1",
			'description' => "",
			'layoutAdvanced' => 1,
			'layoutRow' => 1,
			'layoutWidth' => 40,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "useDeclareFetch",
			'owner' => $driver,
			'paramType' => "boolean",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.driver.odbc.param7', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Additional arguments",
			'defaultValue' => "",
			'description' => "; separated list of extra arguments to use in the\nODBC connection string.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "extras",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.driver.odbc.param8', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.postgresql.driver.odbc.param6'), 
			$this->getInstanceById('com.mysql.rdbms.postgresql.driver.odbc.param1'), 
			$this->getInstanceById('com.mysql.rdbms.postgresql.driver.odbc.param2'), 
			$this->getInstanceById('com.mysql.rdbms.postgresql.driver.odbc.param3'), 
			$this->getInstanceById('com.mysql.rdbms.postgresql.driver.odbc.param4'), 
			$this->getInstanceById('com.mysql.rdbms.postgresql.driver.odbc.param5'), 
			$this->getInstanceById('com.mysql.rdbms.postgresql.driver.odbc.param7'), 
			$this->getInstanceById('com.mysql.rdbms.postgresql.driver.odbc.param8'), 
		]);

		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "ODBC Data Source",
			'description' => "Pyodbc ODBC Driver, using DSN",
			'driverLibraryName' => "pyodbc",
			'connectionStringTemplate' => "DSN=%dsn%;UseDeclareFetch=%useDeclareFetch%",
			'files' => new \ArrayObject([
			]),
			'filesTarget' => ".",
			'name' => "PostgresqlOdbcDSN",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.driver.odbc_dsn', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "DSN:",
			'defaultValue' => "",
			'description' => "Pre-configured ODBC data source.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueModule' => "DbPostgresqlRE",
			'lookupValueMethod' => "getDataSourceNames",
			'name' => "dsn",
			'owner' => $driver,
			'paramType' => "enum",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.driver.odbc_dsn.param1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Password:",
			'defaultValue' => "",
			'description' => "The user's password. Will be requested later if it's not set.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "password",
			'owner' => $driver,
			'paramType' => "keychain",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.driver.odbc_dsn.param2', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Use Declare Fetch",
			'defaultValue' => "1",
			'description' => "",
			'layoutAdvanced' => 1,
			'layoutRow' => 1,
			'layoutWidth' => 40,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "useDeclareFetch",
			'owner' => $driver,
			'paramType' => "boolean",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.driver.odbc_dsn.param3', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.postgresql.driver.odbc_dsn.param1'), 
			$this->getInstanceById('com.mysql.rdbms.postgresql.driver.odbc_dsn.param2'), 
			$this->getInstanceById('com.mysql.rdbms.postgresql.driver.odbc_dsn.param3'), 
		]);

		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "ODBC (direct connection string)",
			'description' => "Pyodbc ODBC Driver",
			'driverLibraryName' => "pyodbc",
			'connectionStringTemplate' => "%connection_string%",
			'files' => new \ArrayObject([
			]),
			'hostIdentifierTemplate' => "postgresql@DRIVER=%driver%;SERVER=%hostName%;PORT=%port%",
			'filesTarget' => ".",
			'name' => "PostgresqlOdbc",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.driver.odbc_connstr', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Connection String:",
			'defaultValue' => "",
			'description' => "The full ODBC connection string to use for the connection.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "connection_string",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.driver.odbc_connstr.param1', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.postgresql.driver.odbc_connstr.param1'), 
		]);

		$rdbms_postgresql->defaultDriver = $this->getInstanceById('com.mysql.rdbms.postgresql.driver.odbc');


		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['DEC']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "DECIMAL",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "6",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.decimal', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "NUMERIC",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "6",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.numeric', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "FLOAT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.float', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['FLOAT4']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "REAL",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.real', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['FLOAT8']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "DOUBLE PRECISION",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.double', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "MONEY",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "6",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.money', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['INT2']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "SMALLINT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.smallint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['INTEGER', 'INT4']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "INT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.int', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['INT8']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "BIGINT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.bigint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['SERIAL2']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "SMALLSERIAL",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.smallserial', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['SERIAL4']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "SERIAL",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.serial', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['SERIAL8']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "BIGSERIAL",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.bigserial', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['CHARACTER']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "CHAR",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.char', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['CHARACTER VARYING']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "VARCHAR",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.varchar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['NATIONAL CHARACTER']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "NCHAR",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.nchar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['NATIONAL CHARACTER VARYING', 'NCHAR VARYING']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "NVARCHAR",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.nvarchar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "TEXT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.text', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.blob'),
			'name' => "BYTEA",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'needsQuotes' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.bytea', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "DATE",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.date', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['TIME WITH TIME ZONE', 'TIME WITHOUT TIME ZONE', 'TIMETZ']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "TIME",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.time', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['TIMESTAMP WITH TIME ZONE', 'TIMESTAMP WITHOUT TIME ZONE', 'TIMESTAMPTZ']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "TIMESTAMP",
			'needsQuotes' => "1",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.timestamp', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "INTERVAL",
			'needsQuotes' => "1",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.interval', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "BIT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.bit', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['VARBIT']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "BIT VARYING",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.bitvarying', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['BOOL']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "BOOLEAN",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.boolean', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "XML",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.xml', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "JSON",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.json', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "CIDR",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.cidr', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "INET",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.inet', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "MACADDR",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.macaddr', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "TSQUERY",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.tsquery', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "TSVECTOR",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.tsvector', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "TXID_SNAPSHOT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.txid_snapshot', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "UUID",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.uuid', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "ARRAY",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.txid_snapshot', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "BOX",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.box', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "CIRCLE",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.circle', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "LINE",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.line', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "LSEG",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.lseg', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "PATH",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.path', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "POINT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.point', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "POLYGON",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.postgresql')
		]);
		$this->setInstance('com.mysql.rdbms.postgresql.datatype.polygon', $simpleDatatype);

		/** ***********************************************************
		 * sql92
		 ************************************************************ */
		$rdbms_sql92 = new \Mwb\Grt\Db\Mgmt\Rdbms([
			'caption' => "Generic SQL92 Compliant RDBMS",
			'name' => "Sql92",
			'databaseObjectPackage' => "db.sql92",
			'maximumIdentifierLength' => 64,
			'doesSupportCatalogs' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sql92', $rdbms_sql92);
		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "ODBC (manually entered parameters)",
			'description' => "Pyodbc ODBC Driver",
			'driverLibraryName' => "pyodbc",
			'connectionStringTemplate' => "DRIVER=%driver%;SERVER=%hostName%;PORT=%port%;DATABASE=%schema%;UID=%userName%;PWD=%password%;%extras%",
			'files' => new \ArrayObject([
			]),
			'hostIdentifierTemplate' => "sql92@DRIVER=%driver%;SERVER=%hostName%;PORT=%port%",
			'filesTarget' => ".",
			'name' => "Sql92Odbc",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.driver.odbc', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Driver:",
			'defaultValue' => "",
			'description' => "The Driver name of your ODBC driver (E.g. FreeTDS, PostgreSQL).",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "driver",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sql92.driver.odbc.param6', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Hostname:",
			'defaultValue' => "127.0.0.1",
			'description' => "Name or IP address of the server host.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 118,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "hostName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sql92.driver.odbc.param1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Port:",
			'defaultValue' => "",
			'description' => "TCP/IP port.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 46,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "port",
			'owner' => $driver,
			'paramType' => "int",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sql92.driver.odbc.param2', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Username:",
			'defaultValue' => "root",
			'description' => "Name of the user to connect with.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "userName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sql92.driver.odbc.param3', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Password:",
			'defaultValue' => "",
			'description' => "The user's password. Will be requested later if it's not set.",
			'layoutAdvanced' => 0,
			'layoutRow' => 3,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "password",
			'owner' => $driver,
			'paramType' => "keychain",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sql92.driver.odbc.param4', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Database:",
			'defaultValue' => "",
			'description' => "The database to connect to. Leave blank to select it later.",
			'layoutAdvanced' => 0,
			'layoutRow' => 4,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "schema",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sql92.driver.odbc.param5', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Additional arguments",
			'defaultValue' => "",
			'description' => "; separated list of extra arguments to use in the\nODBC connection string.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "extras",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sql92.driver.odbc.param7', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.sql92.driver.odbc.param6'), 
			$this->getInstanceById('com.mysql.rdbms.sql92.driver.odbc.param1'), 
			$this->getInstanceById('com.mysql.rdbms.sql92.driver.odbc.param2'), 
			$this->getInstanceById('com.mysql.rdbms.sql92.driver.odbc.param3'), 
			$this->getInstanceById('com.mysql.rdbms.sql92.driver.odbc.param4'), 
			$this->getInstanceById('com.mysql.rdbms.sql92.driver.odbc.param5'), 
			$this->getInstanceById('com.mysql.rdbms.sql92.driver.odbc.param7'), 
		]);

		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "ODBC Data Source",
			'description' => "Pyodbc ODBC Driver, using DSN",
			'driverLibraryName' => "pyodbc",
			'connectionStringTemplate' => "DSN=%dsn%",
			'files' => new \ArrayObject([
			]),
			'filesTarget' => ".",
			'name' => "Sql92OdbcDSN",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.driver.odbc_dsn', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "DSN:",
			'defaultValue' => "",
			'description' => "Pre-configured ODBC data source.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueModule' => "DbSql92RE",
			'lookupValueMethod' => "getDataSourceNames",
			'name' => "dsn",
			'owner' => $driver,
			'paramType' => "enum",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sql92.driver.odbc_dsn.param1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Password:",
			'defaultValue' => "",
			'description' => "The user's password. Will be requested later if it's not set.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "password",
			'owner' => $driver,
			'paramType' => "keychain",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sql92.driver.odbc_dsn.param2', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.sql92.driver.odbc_dsn.param1'), 
			$this->getInstanceById('com.mysql.rdbms.sql92.driver.odbc_dsn.param2'), 
		]);

		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "ODBC (direct connection string)",
			'description' => "Pyodbc ODBC Driver",
			'driverLibraryName' => "pyodbc",
			'connectionStringTemplate' => "%connection_string%",
			'files' => new \ArrayObject([
			]),
			'hostIdentifierTemplate' => "sql92@DRIVER=%driver%;SERVER=%hostName%;PORT=%port%",
			'filesTarget' => ".",
			'name' => "Sql92Odbc",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.driver.odbc_connstr', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Connection String:",
			'defaultValue' => "",
			'description' => "The full ODBC connection string to use for the connection.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "connection_string",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sql92.driver.odbc_connstr.param1', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.sql92.driver.odbc_connstr.param1'), 
		]);

		$rdbms_sql92->defaultDriver = $this->getInstanceById('com.mysql.rdbms.sql92.driver.odbc');


		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['DEC']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "DECIMAL",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "6",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.datatype.decimal', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "NUMERIC",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "6",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.datatype.numeric', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "FLOAT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.datatype.float', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "REAL",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.datatype.real', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "DOUBLE PRECISION",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.datatype.double', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "SMALLINT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.datatype.smallint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['INTEGER']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "INT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.datatype.int', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "BIGINT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.datatype.bigint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['CHARACTER']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "CHAR",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.datatype.char', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['CHARACTER VARYING']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "VARCHAR",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.datatype.varchar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['NATIONAL CHARACTER']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "NCHAR",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.datatype.nchar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['NATIONAL CHARACTER VARYING', 'NCHAR VARYING']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "NVARCHAR",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.datatype.nvarchar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['BINARY LARGE OBJECT']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.blob'),
			'name' => "BLOB",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'needsQuotes' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.datatype.blob', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['CHARACTER LARGE OBJECT']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.blob'),
			'name' => "CLOB",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'needsQuotes' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.datatype.clob', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "DATE",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.datatype.date', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['TIME WITH TIME ZONE', 'TIME WITHOUT TIME ZONE']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "TIME",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.datatype.time', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['TIMESTAMP WITH TIME ZONE', 'TIMESTAMP WITHOUT TIME ZONE']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "TIMESTAMP",
			'needsQuotes' => "1",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.datatype.timestamp', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "INTERVAL",
			'needsQuotes' => "1",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.datatype.interval', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "BIT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.datatype.bit', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "BIT VARYING",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.datatype.bitvarying', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "BOOLEAN",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.datatype.boolean', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "XML",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sql92')
		]);
		$this->setInstance('com.mysql.rdbms.sql92.datatype.xml', $simpleDatatype);

		/** ***********************************************************
		 * sqlanywhere
		 ************************************************************ */
		$rdbms_sqlanywhere = new \Mwb\Grt\Db\Mgmt\Rdbms([
			'caption' => "Sybase SQLAnywhere",
			'name' => "SQLAnywhere",
			'databaseObjectPackage' => "db.sqlanywhere",
			'maximumIdentifierLength' => 64,
			'doesSupportCatalogs' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere', $rdbms_sqlanywhere);
		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "ODBC (manually entered parameters)",
			'description' => "Pyodbc ODBC Driver",
			'driverLibraryName' => "pyodbc",
			'connectionStringTemplate' => "DRIVER=%driver%;HOST=%hostName%:%port%;DATABASEFILE=%schema%;UID=%userName%;%extras%",
			'files' => new \ArrayObject([
			]),
			'hostIdentifierTemplate' => "sqlanywhere@DRIVER=%driver%;SERVER=%hostName%;PORT=%port%",
			'filesTarget' => ".",
			'name' => "SQLAnywhereOdbc",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.driver.odbc', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Driver:",
			'defaultValue' => "SQLAnywhere",
			'description' => "The Driver name of your ODBC driver.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "driver",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.driver.odbc.param1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Hostname:",
			'defaultValue' => "127.0.0.1",
			'description' => "Name or IP address of the server host.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 118,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "hostName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.driver.odbc.param2', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Port:",
			'defaultValue' => "2638",
			'description' => "TCP/IP port.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 46,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "port",
			'owner' => $driver,
			'paramType' => "int",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.driver.odbc.param3', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Username:",
			'defaultValue' => "dba",
			'description' => "Name of the user to connect with.",
			'layoutAdvanced' => 0,
			'layoutRow' => 3,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "userName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.driver.odbc.param4', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Password:",
			'defaultValue' => "",
			'description' => "The user's password. Will be requested later if it's not set.",
			'layoutAdvanced' => 0,
			'layoutRow' => 4,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "password",
			'owner' => $driver,
			'paramType' => "keychain",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.driver.odbc.param5', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Database File:",
			'defaultValue' => "",
			'description' => "The database to connect to.",
			'layoutAdvanced' => 0,
			'layoutRow' => 5,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "schema",
			'owner' => $driver,
			'paramType' => "file",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.driver.odbc.param6', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Additional arguments",
			'defaultValue' => "",
			'description' => "; separated list of extra arguments to use in the\nODBC connection string.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "extras",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.driver.odbc.param7', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.sqlanywhere.driver.odbc.param1'), 
			$this->getInstanceById('com.mysql.rdbms.sqlanywhere.driver.odbc.param2'), 
			$this->getInstanceById('com.mysql.rdbms.sqlanywhere.driver.odbc.param3'), 
			$this->getInstanceById('com.mysql.rdbms.sqlanywhere.driver.odbc.param4'), 
			$this->getInstanceById('com.mysql.rdbms.sqlanywhere.driver.odbc.param5'), 
			$this->getInstanceById('com.mysql.rdbms.sqlanywhere.driver.odbc.param6'), 
			$this->getInstanceById('com.mysql.rdbms.sqlanywhere.driver.odbc.param7'), 
		]);

		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "ODBC Data Source",
			'description' => "Pyodbc ODBC Driver, using DSN",
			'driverLibraryName' => "pyodbc",
			'connectionStringTemplate' => "DSN=%dsn%",
			'files' => new \ArrayObject([
			]),
			'filesTarget' => ".",
			'name' => "SQLAnywhereOdbcDSN",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.driver.odbc_dsn', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "DSN:",
			'defaultValue' => "",
			'description' => "Pre-configured ODBC data source.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueModule' => "DbSQLAnywhereRE",
			'lookupValueMethod' => "getDataSourceNames",
			'name' => "dsn",
			'owner' => $driver,
			'paramType' => "enum",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.driver.odbc_dsn.param1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Password:",
			'defaultValue' => "",
			'description' => "The user's password. Will be requested later if it's not set.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "password",
			'owner' => $driver,
			'paramType' => "keychain",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.driver.odbc_dsn.param2', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.sqlanywhere.driver.odbc_dsn.param1'), 
			$this->getInstanceById('com.mysql.rdbms.sqlanywhere.driver.odbc_dsn.param2'), 
		]);

		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "ODBC (direct connection string)",
			'description' => "Pyodbc ODBC Driver",
			'driverLibraryName' => "pyodbc",
			'connectionStringTemplate' => "%connection_string%",
			'files' => new \ArrayObject([
			]),
			'hostIdentifierTemplate' => "sqlanywhere@DRIVER=%driver%;SERVER=%hostName%;PORT=%port%",
			'filesTarget' => ".",
			'name' => "SQLAnywhereOdbc",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.driver.odbc_connstr', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Connection String:",
			'defaultValue' => "",
			'description' => "The full ODBC connection string to use for the connection.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "connection_string",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.driver.odbc_connstr.param1', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.sqlanywhere.driver.odbc_connstr.param1'), 
		]);

		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "sqlanydb",
			'description' => "Python DB API module for Sybase SQLAnywhere",
			'driverLibraryName' => "sqlanydb",
			'connectionStringTemplate' => "{'host':\"\"\"%hostName%:%port%\"\"\",'userid':\"\"\"%userName%\"\"\",'databasename':\"\"\"%dbName%\"\"\",'servername':\"\"\"%serverName%\"\"\",'password':\"\"\"%password%\"\"\"}",
			'files' => new \ArrayObject([
			]),
			'hostIdentifierTemplate' => "sqlanywhere@DRIVER=sqlanydb;SERVER=%hostName%;PORT=%port%",
			'filesTarget' => ".",
			'name' => "SQLAnyDB",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.driver.sqlanydb', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Host:",
			'defaultValue' => "127.0.0.1",
			'description' => "Host name or IP address of your server",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 118,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "hostName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.driver.sqlanydb.param1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Port:",
			'defaultValue' => "2638",
			'description' => "TCP/IP Port",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 46,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "port",
			'owner' => $driver,
			'paramType' => "int",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.driver.sqlanydb.param2', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Server Name:",
			'defaultValue' => "",
			'description' => "Server and Database names",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 109,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "serverName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.driver.sqlanydb.param3', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Database Name:",
			'defaultValue' => "demo",
			'description' => "",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 109,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "dbName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.driver.sqlanydb.param4', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Username:",
			'defaultValue' => "dba",
			'description' => "Name of the connection user.",
			'layoutAdvanced' => 0,
			'layoutRow' => 3,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "userName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.driver.sqlanydb.param5', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Password:",
			'defaultValue' => "",
			'description' => "The user's password. Will be requested later if it's not set.",
			'layoutAdvanced' => 0,
			'layoutRow' => 4,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "password",
			'owner' => $driver,
			'paramType' => "keychain",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.driver.sqlanydb.param6', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.sqlanywhere.driver.sqlanydb.param1'), 
			$this->getInstanceById('com.mysql.rdbms.sqlanywhere.driver.sqlanydb.param2'), 
			$this->getInstanceById('com.mysql.rdbms.sqlanywhere.driver.sqlanydb.param3'), 
			$this->getInstanceById('com.mysql.rdbms.sqlanywhere.driver.sqlanydb.param4'), 
			$this->getInstanceById('com.mysql.rdbms.sqlanywhere.driver.sqlanydb.param5'), 
			$this->getInstanceById('com.mysql.rdbms.sqlanywhere.driver.sqlanydb.param6'), 
		]);

		$rdbms_sqlanywhere->defaultDriver = $this->getInstanceById('com.mysql.rdbms.sqlanywhere.driver.odbc');


		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "BIT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.bit', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "DECIMAL",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "6",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.decimal', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "NUMERIC",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "6",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.numeric', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "FLOAT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.float', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "REAL",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.real', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "DOUBLE",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.double', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "SMALLMONEY",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.smallmoney', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "MONEY",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.money', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "TINYINT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.tinyint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "SMALLINT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.smallint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['INT']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "INTEGER",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.int', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "BIGINT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.bigint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['CHARACTER']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "CHAR",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.char', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['CHARACTER VARYING']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "VARCHAR",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.varchar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['NATIONAL CHARACTER', 'NATIONAL CHAR']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "NCHAR",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.nchar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['NATIONAL CHARACTER VARYING', 'NATIONAL CHAR VARYING', 'NCHAR VARYING']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "NVARCHAR",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.nvarchar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'synonyms' => new \ArrayObject(['LONG VARCHAR']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "TEXT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.text', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'synonyms' => new \ArrayObject(['LONG NVARCHAR']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "NTEXT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.ntext', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "36",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "UNIQUEIDENTIFIERSTR",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.uniqueidentifierstr', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "XML",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.xml', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'synonyms' => new \ArrayObject(['BIT VARYING']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "VARBIT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.varbit', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'synonyms' => new \ArrayObject(['LONG BIT VARYING']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "LONG VARBIT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.longvarbit', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "DATE",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.date', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "TIME",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.time', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['SMALLDATETIME']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "DATETIME",
			'needsQuotes' => "1",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.datetime', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['TIMESTAMP WITH TIME ZONE']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "TIMESTAMP",
			'needsQuotes' => "1",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.timestamp', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "DATETIMEOFFSET",
			'needsQuotes' => "1",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.datetimeoffset', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'synonyms' => new \ArrayObject(['VARBINARY']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.blob'),
			'name' => "BINARY",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "2",
			'needsQuotes' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.binary', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'synonyms' => new \ArrayObject(['IMAGE']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.blob'),
			'name' => "LONG BINARY",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'needsQuotes' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.longbinary', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "ST_GEOMETRY",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.stgeometry', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "ST_POINT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.stpoint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "ST_CURVE",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.stcurve', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "ST_SURFACE",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.stsurface', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "ST_GEOMCOLLECTION",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.stgeomcollection', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "ST_LINESTRING",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.stlinestring', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "ST_CIRCULARSTRING",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.stcircularstring', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "ST_COMPOUNDCURVE",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.stcompoundcurve', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "ST_CURVEPOLYGON",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.stcurvepolygon', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "ST_MULTIPOINT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.stmultipoint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "ST_MULTICURVE",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.stmulticurve', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "ST_MULTISURFACE",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.stmultisurface', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "ST_POLYGON",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.stpolygon', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "ST_MULTILINESTRING",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.stmultilinestring', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "ST_MULTIPOLYGON",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "-1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlanywhere')
		]);
		$this->setInstance('com.mysql.rdbms.sqlanywhere.datatype.stmultipolygon', $simpleDatatype);

		/** ***********************************************************
		 * sqlite
		 ************************************************************ */
		$rdbms_sqlite = new \Mwb\Grt\Db\Mgmt\Rdbms([
			'caption' => "SQLite",
			'name' => "SQLite",
			'databaseObjectPackage' => "db.sqlite",
			'maximumIdentifierLength' => 64,
			'doesSupportCatalogs' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sqlite', $rdbms_sqlite);
		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "SQLite Standard Python Module",
			'description' => "The sqlite3 module in the Python Standard Library",
			'driverLibraryName' => "sqlite3",
			'connectionStringTemplate' => "",
			'files' => new \ArrayObject([
			]),
			'hostIdentifierTemplate' => "sqlite@DRIVER=%driver%;SERVER=%hostName%",
			'filesTarget' => ".",
			'name' => "SQLiteDriver",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.driver.sqlite', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Database File:",
			'defaultValue' => "",
			'description' => "The database to connect to.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "dbfile",
			'owner' => $driver,
			'paramType' => "file",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.driver.odbc.param5', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.sqlite.driver.odbc.param5'), 
		]);

		$rdbms_sqlite->defaultDriver = $this->getInstanceById('com.mysql.rdbms.sqlite.driver.sqlite');


		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "BOOLEAN",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.boolean', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "DECIMAL",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "6",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.decimal', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "NUMERIC",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "6",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.numeric', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'synonyms' => new \ArrayObject(['FLOA']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "FLOAT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.float', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "REAL",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.real', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['DOUB']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "DOUBLE",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.double', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "TINYINT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.tinyint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "SMALLINT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.smallint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "MEDIUMINT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.mediumint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "INT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.int', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "INTEGER",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.integer', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "BIGINT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.bigint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['CHARACTER']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "CHAR",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.char', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['CHARACTER VARYING']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "VARCHAR",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.varchar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['NATIONAL CHARACTER', 'NATIONAL CHAR', 'NATIVE CHARACTER']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "NCHAR",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.nchar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject(['NATIONAL CHARACTER VARYING', 'NATIONAL CHAR VARYING', 'NCHAR VARYING', 'NATIVE CHARACTER VARYING']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "NVARCHAR",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.nvarchar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'synonyms' => new \ArrayObject(['LONG VARCHAR', 'CLOB']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "TEXT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.text', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'synonyms' => new \ArrayObject(['LONG NVARCHAR']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "NTEXT",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.ntext', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "DATE",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.date', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "TIME",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.time', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "TIMESTAMP",
			'needsQuotes' => "1",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.timestamp', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'synonyms' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "DATETIME",
			'needsQuotes' => "1",
			'numericPrecision' => "-1",
			'numericPrecisionRadix' => "-1",
			'numericScale' => "-1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.datetime', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "-1",
			'characterOctetLength' => "-1",
			'dateTimePrecision' => "-1",
			'flags' => new \ArrayObject([]),
			'synonyms' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.blob'),
			'name' => "BLOB",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'needsQuotes' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sqlite')
		]);
		$this->setInstance('com.mysql.rdbms.sqlite.datatype.blob', $simpleDatatype);

		/** ***********************************************************
		 * sybase
		 ************************************************************ */
		$rdbms_sybase = new \Mwb\Grt\Db\Mgmt\Rdbms([
			'caption' => "Sybase ASE",
			'name' => "Sybase",
			'databaseObjectPackage' => "db.sybase",
			'maximumIdentifierLength' => 64,
			'doesSupportCatalogs' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sybase', $rdbms_sybase);
		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "ODBC Data Source",
			'description' => "ODBC Driver, using DSN",
			'driverLibraryName' => "pyodbc",
			'connectionStringTemplate' => "DSN=%dsn%;DATABASE=%schema%;UID=%userName%;PWD={%password%}",
			'files' => new \ArrayObject([
			]),
			'hostIdentifierTemplate' => "Sybase@%dsn%",
			'filesTarget' => ".",
			'name' => "SybaseOdbcDSN",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc_dsn', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "DSN:",
			'defaultValue' => "",
			'description' => "Pre-configured ODBC data source.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueModule' => "DbSybaseRE",
			'lookupValueMethod' => "getDataSourceNames",
			'name' => "dsn",
			'owner' => $driver,
			'paramType' => "enum",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc_dsn.param1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Username:",
			'defaultValue' => "sa",
			'description' => "Leave blank if specified in data source.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "userName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc_dsn.param2', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Password:",
			'defaultValue' => "",
			'description' => "The user's password. Leave blank to input when needed.",
			'layoutAdvanced' => 0,
			'layoutRow' => 3,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "password",
			'owner' => $driver,
			'paramType' => "keychain",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc_dsn.param3', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Database:",
			'defaultValue' => "",
			'description' => "Leave blank if already in data source or to select it later.",
			'layoutAdvanced' => 0,
			'layoutRow' => 4,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "schema",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc_dsn.param4', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc_dsn.param1'), 
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc_dsn.param2'), 
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc_dsn.param3'), 
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc_dsn.param4'), 
		]);

		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "ODBC Data Source (FreeTDS)",
			'description' => "Pyodbc using FreeTDS Driver DSN",
			'driverLibraryName' => "pyodbc",
			'connectionStringTemplate' => "DSN={%dsn%};DATABASE=%schema%;UID=%userName%;TDS_VERSION=%tdsversion%;PWD={%password%}",
			'files' => new \ArrayObject([
			]),
			'hostIdentifierTemplate' => "Sybase@%dsn%",
			'filesTarget' => ".",
			'name' => "SybaseOdbcDSNFreeTDS",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc_ftds_dsn', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "DSN:",
			'defaultValue' => "",
			'description' => "Pre-configured ODBC data source.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueModule' => "DbSybaseRE",
			'lookupValueMethod' => "getDataSourceNames",
			'name' => "dsn",
			'owner' => $driver,
			'paramType' => "enum",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc_ftds_dsn.param1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Username:",
			'defaultValue' => "sa",
			'description' => "Leave blank if specified in data source.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "userName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc_ftds_dsn.param2', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Password:",
			'defaultValue' => "",
			'description' => "The user's password. Leave blank to input when needed.",
			'layoutAdvanced' => 0,
			'layoutRow' => 3,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "password",
			'owner' => $driver,
			'paramType' => "keychain",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc_ftds_dsn.param3', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Protocol Version:",
			'defaultValue' => "4.1",
			'description' => "TDS protocol version to use by the FreeTDS driver.",
			'layoutAdvanced' => 0,
			'layoutRow' => 5,
			'layoutWidth' => 218,
			'lookupValueMethod' => "getTDSProtocolVersionChoices",
			'lookupValueModule' => "DbSybaseRE",
			'name' => "tdsversion",
			'owner' => $driver,
			'paramType' => "enum",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver. odbc_ftds_dsn.param4', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Database:",
			'defaultValue' => "",
			'description' => "Leave blank if already in data source or to select it later.",
			'layoutAdvanced' => 0,
			'layoutRow' => 4,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "schema",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc_dsn.param5', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Driver sends Unicode data as UTF-8",
			'defaultValue' => "1",
			'description' => "Enable if ODBC driver returns Unicode data as UTF-8 instead of UCS-2.\nRequired for FreeTDS.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "ODBCDriverUsesUTF8",
			'owner' => $driver,
			'paramType' => "boolean",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc_ftds_dsn.aparam2', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc_ftds_dsn.param1'), 
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc_ftds_dsn.param2'), 
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc_ftds_dsn.param3'), 
			$this->getInstanceById('com.mysql.rdbms.sybase.driver. odbc_ftds_dsn.param4'), 
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc_dsn.param5'), 
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc_ftds_dsn.aparam2'), 
		]);

		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "ODBC (native)",
			'description' => "Use the Native Sybase ASE ODBC driver",
			'driverLibraryName' => "pyodbc",
			'connectionStringTemplate' => "DRIVER={%driver%};SERVER=%hostName%;DATABASE=%schema%;PORT=%port%;UID=%userName%;PWD={%password%};%extras%",
			'files' => new \ArrayObject([
			]),
			'hostIdentifierTemplate' => "Sybase@DRIVER=%driver%;SERVER=%hostName%;PORT=%port%",
			'filesTarget' => ".",
			'name' => "SybaseOdbcNative",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc_native', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Driver:",
			'defaultValue' => "Adaptive Server Enterprise",
			'description' => "The name of the ODBC driver you are using.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "driver",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc_native.param0', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Hostname:",
			'defaultValue' => "127.0.0.1",
			'description' => "Address\instance name of the server.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "hostName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc_native.param1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Port:",
			'defaultValue' => "5000",
			'description' => "TCP/IP port.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 46,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "port",
			'owner' => $driver,
			'paramType' => "int",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc_native.param2', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Username:",
			'defaultValue' => "sa",
			'description' => "Name of the user to connect with.",
			'layoutAdvanced' => 0,
			'layoutRow' => 3,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "userName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc_native.param3', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Password:",
			'defaultValue' => "",
			'description' => "The user's password. Leave blank to input when needed.",
			'layoutAdvanced' => 0,
			'layoutRow' => 4,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "password",
			'owner' => $driver,
			'paramType' => "keychain",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc_native.param4', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Database:",
			'defaultValue' => "",
			'description' => "The database to connect to. Leave blank to select it later.",
			'layoutAdvanced' => 0,
			'layoutRow' => 5,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "schema",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc_native.param5', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Additional arguments",
			'defaultValue' => "",
			'description' => "; separated list of extra arguments to use in the\nODBC connection string.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "extras",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc_native.aparam2', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc_native.param0'), 
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc_native.param1'), 
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc_native.param2'), 
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc_native.param3'), 
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc_native.param4'), 
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc_native.param5'), 
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc_native.aparam2'), 
		]);

		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "ODBC (FreeTDS)",
			'description' => "Use an ODBC driver to connect to Sybase ASE",
			'driverLibraryName' => "pyodbc",
			'connectionStringTemplate' => "DRIVER={%driver%};SERVER=%hostName%;PORT=%port%;DATABASE=%schema%;UID=%userName%;PWD={%password%};%extras%",
			'files' => new \ArrayObject([
			]),
			'hostIdentifierTemplate' => "Sybase@DRIVER=%driver%;SERVER=%hostName%;PORT=%port%",
			'filesTarget' => ".",
			'name' => "SybaseOdbc",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Driver:",
			'defaultValue' => "FreeTDS",
			'description' => "The name of the ODBC driver you are using (E.g. FreeTDS).",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "driver",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc.param0', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Hostname:",
			'defaultValue' => "127.0.0.1",
			'description' => "Name or IP address of the server host.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 118,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "hostName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc.param1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Port:",
			'defaultValue' => "5000",
			'description' => "TCP/IP port.",
			'layoutAdvanced' => 0,
			'layoutRow' => 2,
			'layoutWidth' => 46,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "port",
			'owner' => $driver,
			'paramType' => "int",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc.param2', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Username:",
			'defaultValue' => "sa",
			'description' => "Name of the user to connect with.",
			'layoutAdvanced' => 0,
			'layoutRow' => 3,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "userName",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc.param3', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Password:",
			'defaultValue' => "",
			'description' => "The user's password. Leave blank to input when needed.",
			'layoutAdvanced' => 0,
			'layoutRow' => 4,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "password",
			'owner' => $driver,
			'paramType' => "keychain",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc.param4', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Database:",
			'defaultValue' => "",
			'description' => "The database to connect to. Leave blank to select it later.",
			'layoutAdvanced' => 0,
			'layoutRow' => 5,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "schema",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc.param5', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Driver sends Unicode data as UTF-8",
			'defaultValue' => "1",
			'description' => "Enable if ODBC driver returns Unicode data as UTF-8\ninstead of UCS-2. Required for FreeTDS.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "ODBCDriverUsesUTF8",
			'owner' => $driver,
			'paramType' => "boolean",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc.aparam1', $driverParameter);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Additional arguments",
			'defaultValue' => "TDS_VERSION=4.1",
			'description' => "; separated list of extra arguments to use in the\nODBC connection string.",
			'layoutAdvanced' => 1,
			'layoutRow' => -1,
			'layoutWidth' => 318,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "extras",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 0
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc.aparam2', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc.param0'), 
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc.param1'), 
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc.param2'), 
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc.param3'), 
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc.param4'), 
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc.param5'), 
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc.aparam1'), 
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc.aparam2'), 
		]);

		$driver = new \Mwb\Grt\Db\Mgmt\PythonDBAPIDriver([
			'caption' => "ODBC (connection string)",
			'description' => "Provide a full ODBC connection string for Sybase ASE",
			'driverLibraryName' => "pyodbc",
			'connectionStringTemplate' => "%connstring%",
			'files' => new \ArrayObject([
			]),
			'hostIdentifierTemplate' => "Sybase@%connstring%",
			'filesTarget' => ".",
			'name' => "SybaseOdbcConnString",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc_connstring', $driver);
		$driverParameter = new \Mwb\Grt\Db\Mgmt\DriverParameter([
			'caption' => "Connection String:",
			'defaultValue' => "",
			'description' => "The full ODBC connection string.",
			'layoutAdvanced' => 0,
			'layoutRow' => 1,
			'layoutWidth' => 218,
			'lookupValueMethod' => "",
			'lookupValueModule' => "",
			'name' => "connstring",
			'owner' => $driver,
			'paramType' => "string",
			'required' => 1
		]);
		$this->setInstance('com.mysql.rdbms.sybase.driver.odbc_connstring.param0', $driverParameter);
		$driver->parameters = new \ArrayObject([
			$this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc_connstring.param0'), 
		]);

		$rdbms_sybase->defaultDriver = $this->getInstanceById('com.mysql.rdbms.sybase.driver.odbc_dsn');


		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "DECIMAL",
			'numericPrecision' => "38",
			'numericPrecisionRadix' => "0",
			'numericScale' => "38",
			'parameterFormatType' => "4",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.decimal', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "NUMERIC",
			'numericPrecision' => "38",
			'numericPrecisionRadix' => "0",
			'numericScale' => "38",
			'parameterFormatType' => "4",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.numeric', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "FLOAT",
			'numericPrecision' => "15",
			'numericPrecisionRadix' => "0",
			'numericScale' => "15",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.float', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "REAL",
			'numericPrecision' => "24",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.real', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject(['UNSIGNED']),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "TINYINT",
			'numericPrecision' => "3",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.tinyint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "SMALLINT",
			'numericPrecision' => "5",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.smallint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "INT",
			'numericPrecision' => "10",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.int', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.numeric'),
			'name' => "BIGINT",
			'numericPrecision' => "19",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.bigint', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "8000",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "CHAR",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.char', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "8000",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "VARCHAR",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.varchar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "4000",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "NCHAR",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.nchar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "4000",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "NVARCHAR",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.nvarchar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "4000",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "UNICHAR",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.unichar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "4000",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "UNIVARCHAR",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.univarchar', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "2147483647",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "TEXT",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.text', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "1073741823",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.text'),
			'name' => "UNITEXT",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.unitext', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "2147483647",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.blob'),
			'name' => "IMAGE",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'needsQuotes' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.image', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "8000",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.blob'),
			'name' => "BINARY",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "2",
			'needsQuotes' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.binary', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "8000",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.blob'),
			'name' => "VARBINARY",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "2",
			'needsQuotes' => "1",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.varbinary', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "8",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "DATETIME",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.datetime', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "6",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "SMALLDATETIME",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.smalldatetime', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "3",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "DATE",
			'needsQuotes' => "1",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.date', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "5",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.datetime'),
			'name' => "TIME",
			'needsQuotes' => "1",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "2",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.time', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "BIT",
			'numericPrecision' => "1",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.bit', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "MONEY",
			'numericPrecision' => "19",
			'numericPrecisionRadix' => "0",
			'numericScale' => "4",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.money', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "0",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.various'),
			'name' => "SMALLMONEY",
			'numericPrecision' => "10",
			'numericPrecisionRadix' => "0",
			'numericScale' => "4",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.smallmoney', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "30",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "SYSNAME",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.sysname', $simpleDatatype);

		$simpleDatatype = new \Mwb\Grt\Db\SimpleDatatype([
			'characterMaximumLength' => "255",
			'characterOctetLength' => "0",
			'dateTimePrecision' => "0",
			'flags' => new \ArrayObject([]),
			'group' => $this->getInstanceById('com.mysql.rdbms.common.typegroup.string'),
			'name' => "LONGSYSNAME",
			'numericPrecision' => "0",
			'numericPrecisionRadix' => "0",
			'numericScale' => "0",
			'needsQuotes' => "1",
			'parameterFormatType' => "0",
			'owner' => $this->getInstanceById('com.mysql.rdbms.sybase')
		]);
		$this->setInstance('com.mysql.rdbms.sybase.datatype.longsysname', $simpleDatatype);

	}

	private function com_mysql_rdbms_typegroup_Load()
	{
		$typeGroups = [
			'com.mysql.rdbms.common.typegroup.numeric' => new \Mwb\Grt\Db\DatatypeGroup([
				'caption' => 'Numeric Types', 
				'description' => 'Datatypes to store numbers of different sizes', 
				'name' => 'numeric'
			]), 
			'com.mysql.rdbms.common.typegroup.string' => new \Mwb\Grt\Db\DatatypeGroup([
				'caption' => 'Strings', 
				'description' => 'Datatypes to store shorter text', 
				'name' => 'string'
			]), 
			'com.mysql.rdbms.common.typegroup.text' => new \Mwb\Grt\Db\DatatypeGroup([
				'caption' => 'Long Text Types', 
				'description' => 'Datatypes to store long text', 
				'name' => 'text'
			]), 
			'com.mysql.rdbms.common.typegroup.blob' => new \Mwb\Grt\Db\DatatypeGroup([
				'caption' => 'Blob Types', 
				'description' => 'Datatypes to store binary data', 
				'name' => 'blob'
			]), 
			'com.mysql.rdbms.common.typegroup.datetime' => new \Mwb\Grt\Db\DatatypeGroup([
				'caption' => 'Date and Time Types', 
				'description' => 'Datatypes to store date and time values', 
				'name' => 'datetime'
			]), 
			'com.mysql.rdbms.common.typegroup.gis' => new \Mwb\Grt\Db\DatatypeGroup([
				'caption' => 'Geographical Types', 
				'description' => 'Datatypes to store geographical information', 
				'name' => 'gis'
			]), 
			'com.mysql.rdbms.common.typegroup.various' => new \Mwb\Grt\Db\DatatypeGroup([
				'caption' => 'Various Types', 
				'description' => 'Various datatypes', 
				'name' => 'various'
			]), 
			'com.mysql.rdbms.common.typegroup.userdefined' => new \Mwb\Grt\Db\DatatypeGroup([
				'caption' => 'Userdefined Types', 
				'description' => 'Datatypes defined by a user', 
				'name' => 'userdefined'
			]), 
			'com.mysql.rdbms.common.typegroup.structured' => new \Mwb\Grt\Db\DatatypeGroup([
				'caption' => 'Structured Types', 
				'description' => 'Structured datatypes consisting of a collection of simple and other structured datatypes', 
				'name' => 'structured'
			])
		];
		foreach ($typeGroups as $id => $typeGroup) {
			$this->setInstance($id, $typeGroup);
		}
	}


	static public function LoadXml($string)
	{
		$xml = simplexml_load_string($string);

		$loader = new self();

		$loader->com_mysql_rdbms_typegroup_Load();
		$loader->com_mysql_rdbms_Load();
		//$loader->com_mysql_rdbms_datatype_Load();
		//$loader->com_mysql_rdbms_charset_Load();
		$loader->com_mysql_wb_papertype_Load();

		$grtDocument = $loader->loadNodeXml($xml);

		$loader->loadLinkXmlObject($xml);


		//echo get_class($grtDocument) . PHP_EOL;
		//$loader->count=1;
		//$loader->dumpNodeXml('documentElement', $grtDocument->documentElement);

		return $grtDocument;
	}

	protected function dumpNodeXml($name, $objectGrt=null) {
		if ($this->count>4) return;

		$this->indent = str_pad(' ', $this->count*4);

		if (null===$objectGrt) 
			echo $this->indent . ' + ' . $name . ' => Null'. PHP_EOL;
		else if ('object'==gettype($objectGrt)) {
			echo $this->indent . ' + ' . $name . ' => '.get_class($objectGrt) . PHP_EOL;
		}
		else if ('string'==gettype($objectGrt)) {
			echo $this->indent . ' + '.$name.' => "'.$objectGrt .'"'. PHP_EOL;
			return;
		}
		else if ('int'==gettype($objectGrt)) {
			echo $this->indent . ' + '.$name.' => "'.$objectGrt .'"'. PHP_EOL;
			return;
		}
		else if ('double'==gettype($objectGrt)) {
			echo $this->indent . ' + '.$name.' => '.$objectGrt . PHP_EOL;
			return;
		}
		else
			echo $this->indent . ' + '.$name.' => ' . gettype($objectGrt) . PHP_EOL;

		$this->count++;
		foreach((array)$objectGrt as $key=>$value) {
			if ('owner'==$key) {
				continue;
			}

			$this->dumpNodeXml($key, $value);
		}
		$this->count--;
	}

}

