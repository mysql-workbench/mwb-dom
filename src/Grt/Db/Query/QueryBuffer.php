<?php

namespace Mwb\Grt\Db\Query;

/**
 * a proxy to a SQL script editor buffer.\n This object cannot be instantiated
 * directly.
 */
class QueryBuffer extends \Mwb\Grt_Object
{
    /**
     * @var $script ?string full contents of the script editor buffer
     */
    public ?string $script = null;

    /**
     * @var $selectedText ?string selected text
     */
    public ?string $selectedText = null;

    /**
     * @var $currentStatement ?string the SQL statement at current cursor location
     */
    public ?string $currentStatement = null;

    /**
     * @var $selectionStart ?int starting index of text selection
     */
    public ?int $selectionStart = null;

    /**
     * @var $selectionEnd ?int ending index of text selection
     */
    public ?int $selectionEnd = null;

    /**
     * @var $insertionPoint ?int gets or sets the position of the current text
     * insertion point (caret/cursor)
     */
    public ?int $insertionPoint = null;
}

