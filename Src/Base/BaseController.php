<?php
/**
 * @package smr plugin
 */

namespace Src\Base;

class BaseController {
    public $pluginPath;
    public $pluginName;
    public $pluginUrl;

    function __construct() {
        $this->pluginPath   = plugin_dir_path	( dirname(__FILE__, 2));
        $this->pluginUrl    = plugin_dir_url	( dirname(__FILE__, 2));
        $this->pluginName   = plugin_basename	( dirname(__FILE__, 3)) . '/smr-plugin.php';
    }

    /**
     * Just for debug.
     */
    function log($input) {
        file_put_contents($this->pluginPath."/log.text", "--".date(DATE_RFC822)."--".$input."\n", FILE_APPEND);
    }
}