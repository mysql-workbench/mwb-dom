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
					&& (    $parentObjectGrt instanceof  \Mwb\Grt\Db\Schema
						||  $parentObjectGrt instanceof  \Mwb\Db\Table ) ) {
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
	private function com_mysql_rdbms_Load()
	{
		$rdbms = [
			'com.mysql.rdbms.generic' => new \Mwb\Grt\Db\Mgmt\Rdbms(['name'=>'generic']),
			'com.mysql.rdbms.mssql' => new \Mwb\Grt\Db\Mgmt\Rdbms(['name'=>'mssql']),
			'com.mysql.rdbms.mysql' => new \Mwb\Grt\Db\Mgmt\Rdbms(['name'=>'mysql']),
			'com.mysql.rdbms.postgresql' => new \Mwb\Grt\Db\Mgmt\Rdbms(['name'=>'postgresql']),
			'com.mysql.rdbms.sql92' => new \Mwb\Grt\Db\Mgmt\Rdbms(['name'=>'sql92']),
			'com.mysql.rdbms.sqlanywhere' => new \Mwb\Grt\Db\Mgmt\Rdbms(['name'=>'sqlanywhere']),
			'com.mysql.rdbms.sqlite' => new \Mwb\Grt\Db\Mgmt\Rdbms(['name'=>'sqlite']),
			'com.mysql.rdbms.sybase' => new \Mwb\Grt\Db\Mgmt\Rdbms(['name'=>'sybase']),
		];
		foreach ($rdbms as $id => $rdbm) {
			$this->setInstance($id, $rdbm);
		}
	}

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

	private function com_mysql_rdbms_charset_Load()
	{
		$this->com_mysql_rdbms_generic_charset_Load();
		$this->com_mysql_rdbms_mssql_charset_Load();
		$this->com_mysql_rdbms_mysql_charset_Load();
		$this->com_mysql_rdbms_postgresql_charset_Load();
		$this->com_mysql_rdbms_sql92_charset_Load();
		$this->com_mysql_rdbms_sqlanywhere_charset_Load();
		$this->com_mysql_rdbms_sqlite_charset_Load();
		$this->com_mysql_rdbms_sybase_charset_Load();

	}

	private function com_mysql_rdbms_sybase_charset_Load()
	{
		$charsets = [

		];
		foreach ($charsets as $id => $charset) {
			$this->setInstance($id, $charset);
		}
	}

	private function com_mysql_rdbms_sqlite_charset_Load()
	{
		$charsets = [

		];
		foreach ($charsets as $id => $charset) {
			$this->setInstance($id, $charset);
		}
	}

	private function com_mysql_rdbms_sqlanywhere_charset_Load()
	{
		$charsets = [

		];
		foreach ($charsets as $id => $charset) {
			$this->setInstance($id, $charset);
		}
	}

	private function com_mysql_rdbms_sql92_charset_Load()
	{
		$charsets = [

		];
		foreach ($charsets as $id => $charset) {
			$this->setInstance($id, $charset);
		}
	}

	private function com_mysql_rdbms_postgresql_charset_Load()
	{
		$charsets = [

		];
		foreach ($charsets as $id => $charset) {
			$this->setInstance($id, $charset);
		}
	}

	private function com_mysql_rdbms_mysql_charset_Load()
	{
		$charsets = [
			'com.mysql.rdbms.mysql.charset.big5' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['big5_chinese_ci', 'big5_bin'], 
				'defaultCollation' => 'big5_chinese_ci', 
				'description' => 'Big5 Traditional Chinese', 
				'name' => 'big5', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.dec8' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['dec8_swedish_ci', 'dec8_bin'], 
				'defaultCollation' => 'dec8_swedish_ci', 
				'description' => 'DEC West European', 
				'name' => 'dec8', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.cp850' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['cp850_general_ci', 'cp850_bin'], 
				'defaultCollation' => 'cp850_general_ci', 
				'description' => 'DOS West European', 
				'name' => 'cp850', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.hp8' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['hp8_english_ci', 'hp8_bin'], 
				'defaultCollation' => 'hp8_english_ci', 
				'description' => 'HP West European', 
				'name' => 'hp8', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.koi8r' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['koi8r_general_ci', 'koi8r_bin'], 
				'defaultCollation' => 'koi8r_general_ci', 
				'description' => 'KOI8-R Relcom Russian', 
				'name' => 'koi8r', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.latin1' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['latin1_german1_ci', 'latin1_swedish_ci', 'latin1_danish_ci', 'latin1_german2_ci', 'latin1_bin', 'latin1_general_ci', 'latin1_general_cs', 'latin1_spanish_ci'], 
				'defaultCollation' => 'latin1_swedish_ci', 
				'description' => 'ISO 8859-1 West European', 
				'name' => 'latin1', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.latin2' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['latin2_czech_cs', 'latin2_general_ci', 'latin2_hungarian_ci', 'latin2_croatian_ci', 'latin2_bin'], 
				'defaultCollation' => 'latin2_general_ci', 
				'description' => 'ISO 8859-2 Central European', 
				'name' => 'latin2', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.swe7' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['swe7_swedish_ci', 'swe7_bin'], 
				'defaultCollation' => 'swe7_swedish_ci', 
				'description' => '7bit Swedish', 
				'name' => 'swe7', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.ascii' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['ascii_general_ci', 'ascii_bin'], 
				'defaultCollation' => 'ascii_general_ci', 
				'description' => 'US ASCII', 
				'name' => 'ascii', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.ujis' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['ujis_japanese_ci', 'ujis_bin'], 
				'defaultCollation' => 'ujis_japanese_ci', 
				'description' => 'EUC-JP Japanese', 
				'name' => 'ujis', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.sjis' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['sjis_japanese_ci', 'sjis_bin'], 
				'defaultCollation' => 'sjis_japanese_ci', 
				'description' => 'Shift-JIS Japanese', 
				'name' => 'sjis', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.hebrew' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['hebrew_general_ci', 'hebrew_bin'], 
				'defaultCollation' => 'hebrew_general_ci', 
				'description' => 'ISO 8859-8 Hebrew', 
				'name' => 'hebrew', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.tis620' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['tis620_thai_ci', 'tis620_bin'], 
				'defaultCollation' => 'tis620_thai_ci', 
				'description' => 'TIS620 Thai', 
				'name' => 'tis620', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.euckr' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['euckr_korean_ci', 'euckr_bin'], 
				'defaultCollation' => 'euckr_korean_ci', 
				'description' => 'EUC-KR Korean', 
				'name' => 'euckr', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.koi8u' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['koi8u_general_ci', 'koi8u_bin'], 
				'defaultCollation' => 'koi8u_general_ci', 
				'description' => 'KOI8-U Ukrainian', 
				'name' => 'koi8u', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.gb2312' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['gb2312_chinese_ci', 'gb2312_bin'], 
				'defaultCollation' => 'gb2312_chinese_ci', 
				'description' => 'GB2312 Simplified Chinese', 
				'name' => 'gb2312', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.greek' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['greek_general_ci', 'greek_bin'], 
				'defaultCollation' => 'greek_general_ci', 
				'description' => 'ISO 8859-7 Greek', 
				'name' => 'greek', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.cp1250' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['cp1250_general_ci', 'cp1250_czech_cs', 'cp1250_croatian_ci', 'cp1250_bin'], 
				'defaultCollation' => 'cp1250_general_ci', 
				'description' => 'Windows Central European', 
				'name' => 'cp1250', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.gbk' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['gbk_chinese_ci', 'gbk_bin'], 
				'defaultCollation' => 'gbk_chinese_ci', 
				'description' => 'GBK Simplified Chinese', 
				'name' => 'gbk', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.latin5' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['latin5_turkish_ci', 'latin5_bin'], 
				'defaultCollation' => 'latin5_turkish_ci', 
				'description' => 'ISO 8859-9 Turkish', 
				'name' => 'latin5', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.asmscii8' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['armscii8_general_ci', 'armscii8_bin'], 
				'defaultCollation' => 'armscii8_general_ci', 
				'description' => 'ARMSCII-8 Armenian', 
				'name' => 'armscii8', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.utf8' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['utf8_bin', 'utf8_czech_ci', 'utf8_danish_ci', 'utf8_esperanto_ci', 'utf8_estonian_ci', 'utf8_general_ci', 'utf8_general_mysql500_ci', 'utf8_hungarian_ci', 'utf8_icelandic_ci', 'utf8_latvian_ci', 'utf8_lithuanian_ci', 'utf8_persian_ci', 'utf8_polish_ci', 'utf8_roman_ci', 'utf8_romanian_ci', 'utf8_sinhala_ci', 'utf8_slovak_ci', 'utf8_slovenian_ci', 'utf8_spanish_ci', 'utf8_spanish2_ci', 'utf8_swedish_ci', 'utf8_turkish_ci', 'utf8_unicode_ci'], 
				'defaultCollation' => 'utf8_general_ci', 
				'description' => 'UTF-8 Unicode', 
				'name' => 'utf8', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.ucs2' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['ucs2_bin', 'ucs2_czech_ci', 'ucs2_danish_ci', 'ucs2_esperanto_ci', 'ucs2_estonian_ci', 'ucs2_general_ci', 'ucs2_general_mysql500_ci', 'ucs2_hungarian_ci', 'ucs2_icelandic_ci', 'ucs2_latvian_ci', 'ucs2_lithuanian_ci', 'ucs2_persian_ci', 'ucs2_polish_ci', 'ucs2_roman_ci', 'ucs2_romanian_ci', 'ucs2_sinhala_ci', 'ucs2_slovak_ci', 'ucs2_slovenian_ci', 'ucs2_spanish_ci', 'ucs2_spanish2_ci', 'ucs2_swedish_ci', 'ucs2_turkish_ci', 'ucs2_unicode_ci'], 
				'defaultCollation' => 'ucs2_general_ci', 
				'description' => 'UCS-2 Unicode', 
				'name' => 'ucs2', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.cp866' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['cp866_general_ci', 'cp866_bin'], 
				'defaultCollation' => 'cp866_general_ci', 
				'description' => 'DOS Russian', 
				'name' => 'cp866', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.keybcs2' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['keybcs2_general_ci', 'keybcs2_bin'], 
				'defaultCollation' => 'keybcs2_general_ci', 
				'description' => 'DOS Kamenicky Czech-Slovak', 
				'name' => 'keybcs2', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.macce' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['macce_general_ci', 'macce_bin'], 
				'defaultCollation' => 'macce_general_ci', 
				'description' => 'Mac Central European', 
				'name' => 'macce', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.macroman' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['macroman_general_ci', 'macroman_bin'], 
				'defaultCollation' => 'macroman_general_ci', 
				'description' => 'Mac West European', 
				'name' => 'macroman', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.cp852' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['cp852_general_ci', 'cp852_bin'], 
				'defaultCollation' => 'cp852_general_ci', 
				'description' => 'DOS Central European', 
				'name' => 'cp852', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.latin7' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['latin7_estonian_cs', 'latin7_general_ci', 'latin7_general_cs', 'latin7_bin'], 
				'defaultCollation' => 'latin7_general_ci', 
				'description' => 'ISO 8859-13 Baltic', 
				'name' => 'latin7', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.cp1251' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['cp1251_bulgarian_ci', 'cp1251_ukrainian_ci', 'cp1251_bin', 'cp1251_general_ci', 'cp1251_general_cs'], 
				'defaultCollation' => 'cp1251_general_ci', 
				'description' => 'Windows Cyrillic', 
				'name' => 'cp1251', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.cp1256' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['cp1256_general_ci', 'cp1256_bin'], 
				'defaultCollation' => 'cp1256_general_ci', 
				'description' => 'Windows Arabic', 
				'name' => 'cp1256', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.cp1257' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['cp1257_lithuanian_ci', 'cp1257_bin', 'cp1257_general_ci'], 
				'defaultCollation' => 'cp1257_general_ci', 
				'description' => 'Windows Baltic', 
				'name' => 'cp1257', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.binary' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['binary'], 
				'defaultCollation' => 'binary', 
				'description' => 'Binary pseudo charset', 
				'name' => 'binary', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.geostd8' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['geostd8_general_ci', 'geostd8_bin'], 
				'defaultCollation' => 'geostd8_general_ci', 
				'description' => 'GEOSTD8 Georgian', 
				'name' => 'geostd8', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.cp932' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['cp932_japanese_ci', 'cp932_bin'], 
				'defaultCollation' => 'cp932_japanese_ci', 
				'description' => 'SJIS for Windows Japanese', 
				'name' => 'cp932', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.eucjpms' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['eucjpms_japanese_ci', 'eucjpms_bin'], 
				'defaultCollation' => 'eucjpms_japanese_ci', 
				'description' => 'UJIS for Windows Japanese', 
				'name' => 'eucjpms', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.utf8mb4' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['utf8mb4_bin', 'utf8mb4_czech_ci', 'utf8mb4_danish_ci', 'utf8mb4_esperanto_ci', 'utf8mb4_estonian_ci', 'utf8mb4_general_ci', 'utf8mb4_general_mysql500_ci', 'utf8mb4_hungarian_ci', 'utf8mb4_icelandic_ci', 'utf8mb4_latvian_ci', 'utf8mb4_lithuanian_ci', 'utf8mb4_persian_ci', 'utf8mb4_polish_ci', 'utf8mb4_roman_ci', 'utf8mb4_romanian_ci', 'utf8mb4_sinhala_ci', 'utf8mb4_slovak_ci', 'utf8mb4_slovenian_ci', 'utf8mb4_spanish_ci', 'utf8mb4_spanish2_ci', 'utf8mb4_swedish_ci', 'utf8mb4_turkish_ci', 'utf8mb4_unicode_ci'], 
				'defaultCollation' => 'utf8mb4_general_ci', 
				'description' => 'UTF-8 Unicode', 
				'name' => 'utf8mb4', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.utf16' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['utf16_bin', 'utf16_czech_ci', 'utf16_danish_ci', 'utf16_esperanto_ci', 'utf16_estonian_ci', 'utf16_general_ci', 'utf16_general_mysql500_ci', 'utf16_hungarian_ci', 'utf16_icelandic_ci', 'utf16_latvian_ci', 'utf16_lithuanian_ci', 'utf16_persian_ci', 'utf16_polish_ci', 'utf16_roman_ci', 'utf16_romanian_ci', 'utf16_sinhala_ci', 'utf16_slovak_ci', 'utf16_slovenian_ci', 'utf16_spanish_ci', 'utf16_spanish2_ci', 'utf16_swedish_ci', 'utf16_turkish_ci', 'utf16_unicode_ci'], 
				'defaultCollation' => 'utf16_general_ci', 
				'description' => 'UTF-16 Unicode', 
				'name' => 'utf16', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			]), 
			'com.mysql.rdbms.mysql.charset.utf32' => new \Mwb\Grt\Db\CharacterSet([
				'collations' => ['utf32_bin', 'utf32_czech_ci', 'utf32_danish_ci', 'utf32_esperanto_ci', 'utf32_estonian_ci', 'utf32_general_ci', 'utf32_general_mysql500_ci', 'utf32_hungarian_ci', 'utf32_icelandic_ci', 'utf32_latvian_ci', 'utf32_lithuanian_ci', 'utf32_persian_ci', 'utf32_polish_ci', 'utf32_roman_ci', 'utf32_romanian_ci', 'utf32_sinhala_ci', 'utf32_slovak_ci', 'utf32_slovenian_ci', 'utf32_spanish_ci', 'utf32_spanish2_ci', 'utf32_swedish_ci', 'utf32_turkish_ci', 'utf32_unicode_ci'], 
				'defaultCollation' => 'utf32_general_ci', 
				'description' => 'UTF-32 Unicode', 
				'name' => 'utf32', 
				'owner' => $this->getInstanceById('com.mysql.rdbms.mysql')
			])
		];
		foreach ($charsets as $id => $charset) {
			$this->setInstance($id, $charset);
		}
	}

	private function com_mysql_rdbms_mssql_charset_Load()
	{
		$charsets = [

		];
		foreach ($charsets as $id => $charset) {
			$this->setInstance($id, $charset);
		}
	}

	private function com_mysql_rdbms_generic_charset_Load()
	{
		$charsets = [

		];
		foreach ($charsets as $id => $charset) {
			$this->setInstance($id, $charset);
		}
	}


	static public function LoadXml($string)
	{
		$xml = simplexml_load_string($string);

		$loader = new self();
		$loader->com_mysql_rdbms_Load();
		$loader->com_mysql_rdbms_charset_Load();
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

