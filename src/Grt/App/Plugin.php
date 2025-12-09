<?php

namespace Mwb\Grt\App;

/**
 * a plugin that can be registered
 */
class Plugin extends \Mwb\Grt_Object
{
    /**
     * @var $caption ?string the plugin caption
     */
    public ?string $caption = null;

    /**
     * @var $description ?string the plugin description
     */
    public ?string $description = null;

    /**
     * @var $moduleName ?string the module that implements the editor (for dll plugins,
     * it will be the dll name)
     */
    public ?string $moduleName = null;

    /**
     * @var $moduleFunctionName ?string the module function that implements the editor
     * (for dll plugins, the dll function name)
     */
    public ?string $moduleFunctionName = null;

    /**
     * @var $inputValues ?\ArrayObject
     */
    public ?\ArrayObject $inputValues = null;

    /**
     * @var $pluginType ?string one of (normal, gui, standalone). Type of plugin.
     */
    public ?string $pluginType = null;

    /**
     * @var $groups ?\ArrayObject list of group names the plugin belongs to
     */
    public ?\ArrayObject $groups = null;

    /**
     * @var $documentStructNames ?\ArrayObject the types of documents that can be
     * handled by this plugin
     */
    public ?\ArrayObject $documentStructNames = null;

    /**
     * @var $showProgress ?int DEPRECATED. set to 1 to show a progress bar during
     * execution, 2 if the progress is indeterminate
     */
    public ?int $showProgress = null;

    /**
     * @var $rating ?int the rating of this plugin. The plugin with the highest rating
     * will be choosen, if some kind of matching is used
     */
    public ?int $rating = null;

    /**
     * @var $attributes ?\ArrayObject additional application specific attributes
     */
    public ?\ArrayObject $attributes = null;
}

