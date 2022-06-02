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

        $isActive = $stickyButton['enabled']  ??  false;

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
    <?php
        foreach(["instagram", "call", "whatsapp"] as $btnType) {
            $button = $stickyButton[$btnType] ?? "";
            $pinned = $button["pinned"] ? "pinned" : "";

            if ($button['enabled'] == true) {
                echo "<div class='smr-sticky-button-option $pinned'>";
                if($button['iconAsLink'] == true) {
                    echo "<a class='ico' href='$button[text]' style='background-color: $button[color]'>";
                    echo "<i class='fi $btnType' style='font-size: 17px;'></i></a>";
                } else  {
                    echo "<span>".$this->markdownaParser($button['text'] ?? '')."</span>";
                    echo "<div class='ico' style='background-color: $button[color]'>";
                    echo "<i class='fi $btnType' style='font-size: 17px;'></i></div>";
                }
                echo "</div>";
            }
        }
    ?>
            <div id="handbell" class="ico">
                <i class="fi bell"></i>
            </div>
        </div>
    <?php
        //---------------------- sticky button html end ----------------------
    }
}
?>