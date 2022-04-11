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
        $this->pluginPath = plugin_dir_path	( dirname(__FILE__, 2));
        $this->pluginUrl = plugin_dir_url	( dirname(__FILE__, 2));
        $this->pluginName = plugin_basename	( dirname(__FILE__, 3)) . '/smr-plugin.php';
    }

    /**
     * Just for debug.
     */
    function log(string $input) {
        file_put_contents($this->pluginPath."/log.text", "--".date(DATE_RFC822)."--".$input."\n", FILE_APPEND);
    }

    function markdownaParser(string $markdownText): string {
        $markdownText = preg_replace("/^#{1}\s+?(.+?)$/m", "<h1>$1</h1>", $markdownText); // h1
        $markdownText = preg_replace("/^#{2}\s+?(.+?)$/m", "<h2>$1</h2>", $markdownText); // h2
        $markdownText = preg_replace("/^#{3}\s+?(.+?)$/m", "<h3>$1</h3>", $markdownText); // h3
        $markdownText = preg_replace("/^#{4}\s+?(.+?)$/m", "<h4>$1</h4>", $markdownText); // h4
        $markdownText = preg_replace("/^#{5}\s+?(.+?)$/m", "<h5>$1</h5>", $markdownText); // h5
        $markdownText = preg_replace("/^#{6}\s+?(.+?)$/m", "<h6>$1</h6>", $markdownText); // h5
        $markdownText = preg_replace("/\[(.*?)\]\((.+?)\)/", "<a href='$2' target='_blank'>$1</a>", $markdownText); // link
        $markdownText = preg_replace("/\*\*(.+?)\*\*/", "<strong>$1</strong>", $markdownText); // h5
        $markdownText = preg_replace("/\*(.+?)\*/", "<em>$1</em>", $markdownText); // h5
        preg_match("/^(\s*([-\+\*]\s.+\n?)+)$/m", $markdownText, $matches);
        
        foreach($matches as $list) {
            $htmlList = preg_replace("/^\s*[-\+\*]\s(.+\n?)$/m", "<li>$1</li>", $list);
            $markdownText = str_replace($list, "<ul>$htmlList</ul>", $markdownText);
        } 
    
        $markdownText = preg_replace("/(?<=[\w\s\.])\n/", "<br>", $markdownText); // line break
        $markdownText = preg_replace("/\n/", "", $markdownText); // line break
        
        return trim($markdownText);
    }
}