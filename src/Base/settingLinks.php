<?php
/**
 * @package smr plugin
 */
namespace Src\Base;

use \Src\Base\BaseController;

class SettingLinks extends BaseController {
    public  function register() {
		add_filter("plugin_action_links_$this->pluginName" ,array($this,'settingLinks'));
	}

	public function settingLinks($link) {
		$settings_link = '<a href="admin.php?smr-plugin">Settings</a>';
		array_push($link, $settings_link);
		return $link;
	}
}