<?php
/**
 * @package smr plugin
 */

namespace Src\Base;

use \Src\Base\BaseController;

class Enqueue extends BaseController {
    public  function register() {
        wp_register_script('lottiePlayer',"$this->pluginUrl/assets/js/lottie-player.js");
        add_action( 'wp_footer', array($this, 'enqueue'));
    }	
    
    public function enqueue() {
        wp_enqueue_script('lottiePlayer');
	}
}