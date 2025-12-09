<?php

namespace Mwb;

use \Mwb\Loader;
use \Mwb\Exporter;
use \Mwb\Grt\Document as GrtDocument;

/**
 * Allow to parse a MySQL Workbench file (*.mwb)
 *
 * This class is the entry point to a faithful representation of the MySQL
 * Workbench model.
 * The main purpose of this class is to easily and accurately export database
 * model.
 * This class unzip a .mwb file on the fly.
 * And expose the document.mwb.xml as a GrtDocument Model.
 * This class improve/fix MySQL Workbench EER Diagram to obtain a hierarchy of
 * layers imbricate.
 *
 * @usage `MwbDocument::load(file.mwb)->doc->version;`
 * @todo Allow to use the embeded data.db
 * @todo Allow to use the embeded notes
 * @todo Allow to use the embeded images
 * @FIXME Check ZipArchive extension loaded
 * @version $Rev:$
 * @license New BSD
 */
class Document
{
    public const DOCUMENT_MWB_XML = 'document.mwb.xml';

    public const DATA_DB = 'data.db';

    public ?GrtDocument $doc = null;

    public ?array $db = null;

    public ?array $images = null;

    public ?array $notes = null;

    public function __construct()
    {
        $this->db = [];
        $this->images = [];
        $this->notes = [];
    }

    public static function __callStatic($name, $arguments)
    {
        if ('Load'==$name) {
		$mwbDocument = new self();
		return $mwbDocument->_load(...$arguments);
        } else if ('Export'==$name) {
		$mwbDocument = new self();
            return $mwbDocument->_export(...$arguments);
        } else {
        }
    }

    public function __call($name, $arguments)
    {
        if ('load'==$name) {
            return $this->_load(...$arguments);
        } else if ('export'==$name) {
            return $this->_export(...$arguments);
        } else {
        }
    }

    private function _load($filepath)
    {
        $zip = new \ZipArchive();
        if (true !== $zip->open($filepath)) {
        	throw new \RuntimeException("Impossible to open file '$filename'");
        }

        $content = $zip->getFromName(self::DOCUMENT_MWB_XML);
        $zip->close();

        if (false === $content) {
        	throw new \RuntimeException('File "document.mwb.xml" not found in *.mwb');
        }

        $this->doc = Loader::LoadXml($content);
        // TODO: try{}

        // TODO: $mwbDocument->db, $mwbDocument->images, $mwbDocument->notes
        $this->db[self::DATA_DB] = Null;
        $this->images['icon-service.png'] = Null;
        $this->notes['1'] = 'My note';

        return $this;
    }
    private function _export($filepath)
    {
        $zip = new \ZipArchive();
        if (true !== $zip->open($filepath)) {
        	throw new \RuntimeException("Impossible to open file '$filename'");
        }

        $content = $zip->getFromName(self::DOCUMENT_MWB_XML);
        $zip->close();

        if (false === $content) {
        	throw new \RuntimeException('File "document.mwb.xml" not found in *.mwb');
        }

        $output = Exporter::ExportXml($content);

        return $output;
    }

}

