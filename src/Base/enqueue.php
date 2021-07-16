<?php
/**
 * @package smr plugin
 */

namespace Src\Base;

use \Src\Base\BaseController;

class Enqueue extends BaseController {
    public  function register() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue'));
    }	
    
    public function enqueue() {
		wp_enqueue_style('smrStyle', $this->pluginUrl .'lib/style.css');
		wp_enqueue_script('smrScript', $this->pluginUrl . 'lib/script.css');
	}
}