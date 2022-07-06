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
        $isActive = $stickyButton['enabled'] ?? false;

        $pos = $stickyButton["pos"];
        $xoff = $stickyButton["xoffset"];
        // Set
        $style = "";
        if(isset($pos, $xoff) && $xoff > 0) {
            $style = "$pos: $xoff"."px;";
        }

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
        <div id="smr-sticky-button" style="<?= $style ?>" class="<?= $pos ?>">
    <?php
        foreach(["instagram", "call", "whatsapp"] as $btnType) {
            $button = $stickyButton[$btnType] ?? "";
            $pinned = $button["pinned"] ? "pinned" : "";

            if ($button['enabled'] == true) {
                echo "<div class='sticky-option $pinned'>";
                echo "<div class='ico' style='background-color: $button[color]'>";
                echo "<i class='fi $btnType' style='font-size: 17px;'></i></div>";
                if($button['iconAsLink'] == true) {
                    @preg_match_all("/\[\w*\]\(\S*?\)/", $button['text'], $matches);
                    foreach($matches[0] as $match) {
                        echo @preg_replace("/\[(\w*)\]\((\S*)\)/", "<a href='$2' class='$1' target='_blank'></a>", $match); // link
                    }
                } else  {
                    echo "<span>".$this->markdownaParser($button['text'] ?? '')."</span>";
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