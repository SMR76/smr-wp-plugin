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
		wp_enqueue_style ('smrStyle', $this->pluginUrl .'lib/style.css');
		wp_enqueue_script('smrScript', $this->pluginUrl . 'lib/script.js');
	}
    
    // wp_enqueue_style ('smrStyle',   $this->pluginUrl. 'lib/bootstrap-4.5.0-dist/css/bootstrap.min.css');        
    // wp_enqueue_script('smrScript',  $this->pluginUrl. 'lib/jquery/3.5.1/jquery-3.5.1.min.js');
    // wp_enqueue_script('smrScript',  $this->pluginUrl. 'lib/bootstrap-4.5.0-dist/js/bootstrap.min.js');
}