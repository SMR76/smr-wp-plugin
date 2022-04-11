<?php
/**
 * @package smr plugin
 */


namespace Src\Elements;

use \Src\Base\BaseController;

class StickyButton extends BaseController {
    public function register() {
        add_action( 'wp_footer', [$this, 'fixedInfoButton']);
    }

    public function fixedInfoButton() {
        $values = get_option('smr_config_option');
        $stickyButton = $values['sticky_button'] ?? '';
        $isActive = $stickyButton['active']  ??  false;

        if ($isActive == false || is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) {
            return;
        }
        // Bug: The WordPress admin bar will be in conflict if register style is relocated to the class register method.
        wp_register_style('smrStickyButtonStyle', $this->pluginUrl . 'assets/css/sticky-button.css');
        wp_register_script('smrStickyButtonScript', $this->pluginUrl . 'assets/js/sticky-button.js');

        wp_enqueue_style ('smrStickyButtonStyle');
        wp_enqueue_script('smrStickyButtonScript');

        //---------------------- sticky button html start ----------------------
        ?>
        <div id="smr-sticky-button">
            <div class="smr-sticky-button-option <?= $stickyButton['whatsapp_pinned'] ? "pinned" : "" ?>">
                <span>
                    <?= $this->markdownaParser($stickyButton['whatsapp_info'] ?? '');  ?>
                </span>
                <div class="ico"><i class="fab fa-whatsapp" style="font-size: 17px;"></i></div>
            </div>
            <div class="smr-sticky-button-option <?= $stickyButton['call_pinned'] ? "pinned" : "" ?>">
                <span>
                    <?= $this->markdownaParser($stickyButton['call_info'] ?? '');  ?>            
                </span>
                <div class="ico"><i class="fas fa-mobile-alt"></i></div>
            </div>
            <div class="smr-sticky-button-option <?= $stickyButton['insta_pinned'] ? "pinned" : "" ?>">
                <span>
                    <?= $this->markdownaParser($stickyButton['insta_info'] ?? '') ?>
                </span>
                <div class="ico" ><i class="fab fa-instagram"></i></div>
            </div>
            <div id="handbell" class="ico">
                <i class="fas fa-bell"></i>
            </div>
        </div>
        <?php 
        //---------------------- sticky button html end ----------------------
    }
}
?>