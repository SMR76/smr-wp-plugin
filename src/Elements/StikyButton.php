<?php
/**
 * @package smr plugin
 */


namespace Src\Pages;

use \Src\Base\BaseController;
use \Src\Api\SettingsApi;
use \Src\Api\MOF;

class StikyButton extends BaseController{
    public function register() {
        add_action( 'wp_footer', array($this, 'awd_add_floating_info'));

    }

    function awd_add_floating_info () { 
        $message = '';
        if (is_front_page()) {
            $message = 'A message for visitor in the front page';
        } else if (is_page('about')) {
            $message = 'A message for visitor in the about page';
        } else if (is_singular('post')) {
            $message = 'A message for visitor in a single blog post';
        }
    
        if ($message) {
        ?>
    
        <div class="sticky-slider">
           Call now: 01234567XX
        </div>
        
        <?php 
        }
    }
}