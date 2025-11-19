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
					||  $parentObjectGrt instanceof  \Mwb\Db\Table )) {
						$objectGrt[$nodeObjectGrt->name] = $nodeObjectGrt;
					} else {
						$objectGrt[count($objectGrt)] = $nodeObjectGrt;
					}
					break;
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
	/*
          <value _ptr_="0x55a0c464cf10" type="list" content-type="object" content-struct-name="db.SimpleDatatype" key="simpleDatatypes">
          </value>
	*/
	private function com_mysql_rdbms_mysql_datatype_Load()
	{
		$simpleDatatypes = [
			'com.mysql.rdbms.mysql.datatype.tinyint'     => 'int',
			'com.mysql.rdbms.mysql.datatype.smallint'    => 'int',
			'com.mysql.rdbms.mysql.datatype.mediumint'   => 'int',
			'com.mysql.rdbms.mysql.datatype.int'         => 'int',
			'com.mysql.rdbms.mysql.datatype.bigint'      => 'int',
			'com.mysql.rdbms.mysql.datatype.float'       => 'float',
			'com.mysql.rdbms.mysql.datatype.real'        => 'float',
			'com.mysql.rdbms.mysql.datatype.double'      => 'float',
			'com.mysql.rdbms.mysql.datatype.decimal'     => 'float',
			'com.mysql.rdbms.mysql.datatype.char'        => 'string',
			'com.mysql.rdbms.mysql.datatype.nchar'       => 'string',
			'com.mysql.rdbms.mysql.datatype.varchar'     => 'string',
			'com.mysql.rdbms.mysql.datatype.nvarchar'    => 'string',
			'com.mysql.rdbms.mysql.datatype.binary'      => 'string',
			'com.mysql.rdbms.mysql.datatype.varbinary'   => 'string',
			'com.mysql.rdbms.mysql.datatype.tinytext'    => 'string',
			'com.mysql.rdbms.mysql.datatype.text'        => 'string',
			'com.mysql.rdbms.mysql.datatype.mediumtext'  => 'string',
			'com.mysql.rdbms.mysql.datatype.longtext'    => 'string',
			'com.mysql.rdbms.mysql.datatype.tinyblob'    => 'string',
			'com.mysql.rdbms.mysql.datatype.blob'        => 'string',
			'com.mysql.rdbms.mysql.datatype.mediumblob'  => 'string',
			'com.mysql.rdbms.mysql.datatype.longblob'    => 'string',
			'com.mysql.rdbms.mysql.datatype.json'        => 'string',
			'com.mysql.rdbms.mysql.datatype.datetime'    => '\\DateTime',
			'com.mysql.rdbms.mysql.datatype.datetime_f'  => '\\DateTime',
			'com.mysql.rdbms.mysql.datatype.date'        => '\\DateTime',
			'com.mysql.rdbms.mysql.datatype.time'        => 'int',
			'com.mysql.rdbms.mysql.datatype.time_f'      => 'int',
			'com.mysql.rdbms.mysql.datatype.year'        => 'int',
			'com.mysql.rdbms.mysql.datatype.timestamp'   => 'int',
			'com.mysql.rdbms.mysql.datatype.timestamp_f' => 'int',
			'com.mysql.rdbms.mysql.datatype.geometry'    => 'mixed',
			'com.mysql.rdbms.mysql.datatype.point'       => 'mixed',
			'com.mysql.rdbms.mysql.datatype.linestring'  => 'mixed',
			'com.mysql.rdbms.mysql.datatype.polygon'     => 'mixed',
			'com.mysql.rdbms.mysql.datatype.geometrycollection' => 'mixed',
			'com.mysql.rdbms.mysql.datatype.multipoint'  => 'mixed',
			'com.mysql.rdbms.mysql.datatype.multilinestring' => 'mixed',
			'com.mysql.rdbms.mysql.datatype.multipolygon' => 'mixed',
			'com.mysql.rdbms.mysql.datatype.bit'         => 'mixed',
			'com.mysql.rdbms.mysql.datatype.enum'        => 'mixed',
			'com.mysql.rdbms.mysql.datatype.set'         => 'mixed',
		];
		foreach ($simpleDatatypes as $id => $simpleDatatype) {
			$dataType = new \Mwb\Grt\Db\SimpleDatatype();
			$dataType->name = $id;//$simpleDatatype;
			$this->setInstance($id, $dataType);
		}
	}
	private function com_mysql_wb_papertype_Load()
	{
		$paperTypes = [
			'com.mysql.wb.papertype.a4'     => 'a4',
		];
		foreach ($paperTypes as $id => $unused) {
			$paperType = new \Mwb\Grt\App\PaperType();
			$paperType->name = $id;
			$this->setInstance($id, $paperType);
		}
	}
	private function com_mysql_rdbms_Load()
	{
		$rdbms = [
			'com.mysql.rdbms.mysql'     => 'mysql',
		];
		foreach ($rdbms as $id => $unused) {
			$rdbmType = new \Mwb\Grt\Db\Mgmt\Rdbms();
			$rdbmType->name = $id;
			$this->setInstance($id, $rdbmType);
		}
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


	static public function LoadXml($string)
	{
		$xml = simplexml_load_string($string);

		$loader = new self();
		$loader->com_mysql_rdbms_Load();
		$loader->com_mysql_rdbms_mysql_datatype_Load();
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

