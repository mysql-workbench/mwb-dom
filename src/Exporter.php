<?php

declare(strict_types=1);



namespace Mwb;


class Exporter {

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

	public function exportValueXmlObject($xml) {
		$output = '';

		$key        = !empty($xml['key'])         ? ' key="'.((string)$xml['key']).'"' : '';
		$type       = !empty($xml['type'])        ? (string)$xml['type']          : null;
		$structName = !empty($xml['struct-name']) ? (string)$xml['struct-name']   : null;
		$id         = !empty($xml['id'])          ? (string)$xml['id']            : null;

		$tab = $this->indent;
		if (count($xml->children())) {
			$output .= $tab.'<object class="'.$structName.'" id="'.$id.'"'.$key.'>'.PHP_EOL;
			$output .= $this->exportChildrenXml($xml);
			$output .= $tab.'</object>'.PHP_EOL;
		} else {
			$output .= $tab.'<object class="'.$structName.'" id="'.$id.'"'.$key.' />'.PHP_EOL;
		}

		return $output;
	}

	public function exportValueXmlArray($xml) {
		$output = '';

		$key        = !empty($xml['key'])         ? ' key="'.((string)$xml['key']).'"' : '';
		$type       = !empty($xml['type'])        ? (string)$xml['type']          : null;
		$structName = !empty($xml['struct-name']) ? (string)$xml['struct-name']   : null;
		$id         = !empty($xml['id'])          ? (string)$xml['id']            : null;

		$tab = $this->indent;
		if (count($xml->children())) {
			$output .= $tab.'<array'.$key.'>'.PHP_EOL;
			$output .= $this->exportChildrenXml($xml);
			$output .= $tab.'</array>'.PHP_EOL;
		} else {
			$output .= $tab.'<array id="'.$id.'"'.$key.' />'.PHP_EOL;
		}

		return $output;
	}

	public function exportValueXmlString($xml) {
		$output = '';

		$key        = !empty($xml['key'])         ? 'key="'.((string)$xml['key']).'"' : '';
		$type       = !empty($xml['type'])        ? (string)$xml['type']          : null;
		$structName = !empty($xml['struct-name']) ? (string)$xml['struct-name']   : null;
		$id         = !empty($xml['id'])          ? (string)$xml['id']            : null;
		$str        = !empty((string)$xml)        ? (string)$xml                  : '';

		$tab = $this->indent;
		if (!empty($str)) {
			$output .= $tab.'<string '.$key.'>';
			$output .= $str;
			$output .= '</string>'.PHP_EOL;
		} else {
			$output .= $tab.'<string '.$key.'/>'.PHP_EOL;
		}

		return $output;
	}

	public function exportValueXmlInteger($xml) {
		$output = '';

		$key        = !empty($xml['key'])         ? 'key="'.((string)$xml['key']).'"' : '';
		$type       = !empty($xml['type'])        ? (string)$xml['type']          : null;
		$structName = !empty($xml['struct-name']) ? (string)$xml['struct-name']   : null;
		$id         = !empty($xml['id'])          ? (string)$xml['id']            : null;
		$str        = !empty((string)$xml)        ? (string)$xml                  : '';

		$tab = $this->indent;
		if (!empty($str)) {
			$output .= $tab.'<integer '.$key.'>';
			$output .= $str;
			$output .= '</integer>'.PHP_EOL;
		} else {
			$output .= $tab.'<integer '.$key.'/>'.PHP_EOL;
		}

		return $output;
	}

	public function exportValueXmlDouble($xml) {
		$output = '';

		$key        = !empty($xml['key'])         ? 'key="'.((string)$xml['key']).'"' : '';
		$type       = !empty($xml['type'])        ? (string)$xml['type']          : null;
		$structName = !empty($xml['struct-name']) ? (string)$xml['struct-name']   : null;
		$id         = !empty($xml['id'])          ? (string)$xml['id']            : null;
		$str        = !empty((string)$xml)        ? (string)$xml                  : '';

		$tab = $this->indent;
		if (!empty($str)) {
			$output .= $tab.'<double '.$key.'>';
			$output .= $str;
			$output .= '</double>'.PHP_EOL;
		} else {
			$output .= $tab.'<double '.$key.'/>'.PHP_EOL;
		}

		return $output;
	}

	public function exportValueXmlClass($xml) {
		$output = '';

		$key        = !empty($xml['key'])         ? 'key="'.((string)$xml['key']).'"' : '';
		$type       = !empty($xml['type'])        ? (string)$xml['type']          : null;
		$structName = !empty($xml['struct-name']) ? (string)$xml['struct-name']   : null;
		$id         = !empty($xml['id'])          ? (string)$xml['id']            : null;
		$str        = !empty((string)$xml)        ? (string)$xml                  : '';

		$tab = $this->indent;
		if (count($xml->children())) {
			$output .= $tab.'<dict id="'.$id.'"'.$key.'>'.PHP_EOL;
			$output .= $this->exportChildrenXml($xml);
			$output .= $tab.'</dict>'.PHP_EOL;
		} else {
			$output .= $tab.'<dict '.$key.'/>'.PHP_EOL;
		}

		return $output;
	}

	public function exportValueXml($xml) {
		$output = '';
		$type = !empty($xml['type']) ? (string)$xml['type'] : null;
		$key = !empty($xml['key']) ? (string)$xml['key'] : null;
		switch ($type) {
			case 'object':
				$output .= $this->exportValueXmlObject($xml);
				break;
			case 'list':
				$output .= $this->exportValueXmlArray($xml);
				break;
			case 'dict':
				$output .= $this->exportValueXmlClass($xml);
				break;
			case 'string':
				$output .= $this->exportValueXmlString($xml);
				break;
			case 'int':
				$output .= $this->exportValueXmlInteger($xml);
				break;
			case 'real':
				$output .= $this->exportValueXmlDouble($xml);
				break;
			default :
				throw new \Exception("Something whent wrong : " . $type);
				break;
		}
		return $output;
	}

	public function exportLinkXml($xml, $parentObjectGrt=null) {
		$output = '';

		$type = !empty($xml['type']) ? (string)$xml['type'] : null;
		$key = !empty($xml['key']) ? 'key="'.((string)$xml['key']).'"' : '';
		$struct_name = !empty($xml['struct-name']) ? (string)$xml['struct-name'] : null;

		$id = (string) $xml;

		$output .= $this->indent.'<link '.$key.'>'.$id.'</link>'.PHP_EOL;

		return $output;
	}

	public function exportDataXml($xml) {
		$output = '';
		// check version

		$output .= '<?xml version="1.0"?>'.PHP_EOL;
		$output .= '<data grt_format="2.0" document_type="MySQL Workbench Model" version="1.4.4">'.PHP_EOL;
		$output .= $this->exportNodeXml($xml->value);
		$output .= '</data>'.PHP_EOL;

		return $output;
	}

	public $count = 0;
	protected function exportChildrenXml($xml) {
		$output = '';
		foreach ($xml->children() as $nodeXml)
		{
			$output .= $this->exportNodeXml($nodeXml);
		}
		return $output;
	}

	protected function exportNodeXml($xml, $parentObjectGrt=null) {
		$this->count++;
		$this->indent = str_pad(' ', $this->count*2);

		$output = '';
		$type = !empty($xml['type']) ? (string)$xml['type'] : null;

		if ('value'==$xml->getName()) {
			$output .= $this->exportValueXml($xml);
		} else if('data'==$xml->getName()) {
			$output .= $this->exportDataXml($xml);
		} else if('link'==$xml->getName()) {
			$output .= $this->exportLinkXml($xml);
		} else {
			throw new \Exception("Unexpected reach");
		}

		$this->count--;
		$this->indent = str_pad(' ', $this->count*4);

		return $output;
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


	static public function ExportXml($string)
	{
		$xml = simplexml_load_string($string);

		$exporter = new self();
		$output = $exporter->exportNodeXml($xml);

		return $output;
	}
}

