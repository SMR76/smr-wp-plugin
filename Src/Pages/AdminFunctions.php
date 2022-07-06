<?php
/**
 * @package smr plugin
 */

namespace Src\Pages;

use \Src\Base\BaseController;

class AdminFunctions extends BaseController {
    private $cssDir = "ltr";

    public function __construct() {
        parent::__construct();
        $this->cssDir = is_rtl() ? "rtl" : "ltr";
    }

    /**
     * admin setting page (general page)
     */
    public function adminGeneralPage() {
        return require_once($this->pluginPath."/templates/adminPage.php");
    }

    /**
     * sanitizes the input fields
     */
    public function optionGroupFieldsFilter($input) {
        // if need a walk.
        // array_walk($input, function ($value, $key) { $value; });
        $stickyButton = &$input["sticky_button"];
        $stickyButton["xoffset"] = $stickyButton["left"] + $stickyButton["right"];

        if($stickyButton["left"] >= 0) {
            $stickyButton["pos"] = "left";
        } else if($stickyButton["right"] >= 0) {
            $stickyButton["pos"] = "right";
        }
        unset($stickyButton["left"], $stickyButton["right"]);

        return $input;
    }

    public function wholesaleSection() {
        echo "<i class='fi handshake small'></i> ";
        _e('Wholesale users settings','smr-plugin');
    }

    public function smsPanelOptionSection() {
        echo "<i class='fi message-processing small'></i> ";
        _e('SMS panel settings','smr-plugin');
    }

    public function checkoutSection() {
        echo "<i class='fi archive-check-outline small'></i> ";
        _e('Checkout settings.','smr-plugin');
    }

    public function contactFormSection() {
        echo "<i class='fi card-account-mail small'></i> ";
        _e('Contact form settings.','smr-plugin');
    }

    public function userActionsSection() {
        echo "<i class='fi account-clock-outline small'></i> ";
        _e('Settings for user actions, such as sending SMS messages following registration.','smr-plugin');
    }

    public function stickyButtonSettings() {
        echo "<i class='fi gesture-tap-button small'></i> ";
        _e('Sticky button settings.','smr-plugin');
    }

    // ---------------------- wholesale -----------------------------
    /**
     * inputs for wholesale section.
     */
    public function activateWholesale() {
        $values = get_option('smr_config_option', []);
        $wholesale = $values['wholesale'];
        ?>
        <input id="ws_active" type='checkbox' value="checked" name='smr_config_option[wholesale][active]' <?= $wholesale['active'] ?>>
        <?php
    }

    public function wholesaleRolesInput() {
        global $wp_roles;
        $allRoleNames = $wp_roles->get_names();

        $values = get_option('smr_config_option', []);
        $wholesale = $values['wholesale'];
        $wsRoles = $wholesale['roles'] ?? "";
        $wsDisplay = $wholesale['active'] ? "initial" : "none";

        $dropdown = "<select name='smr_config_option[wholesale][roles][]' id='ws_roles' multiple style='display:$wsDisplay'>";
        foreach($allRoleNames as $roles ) {
            $dropdown .= "<option value='$roles' ".selected(in_array($roles, $wsRoles),true,false).">$roles</option>";
        }
        $dropdown .= '</select>';
        echo $dropdown;
    }

    // ---------------------- sms panel -----------------------------
    /**
     * inputs for sms panel
     */
    public function smsPanelSettings() {
        $values = get_option('smr_config_option', []);
        $smsPanel = $values['sms_panel'] ?? '';

        $username = $smsPanel['sms_username'] ?? '';
        $password = $smsPanel['sms_password'] ?? '';
        $wsdlApi = $smsPanel['wsdl_api'] ?? '';

        ?>
        <label>
            <i class='fi account-edit big'></i>
            SMS panel username:<br/>
            <input id="sms_username" name="smr_config_option[sms_panel][sms_username]" placeholder="<?= __("sms username" ,"smr-plugin")?>"
                type="text" class="regular-text ltr" value="<?= $username;?>">
        </label><br/>
        <label>
            <i class='fi form-textbox-password big'></i>
            SMS panel password:<br/>
            <input id="sms_password" name="smr_config_option[sms_panel][sms_password]" placeholder="<?= __("sms password" ,"smr-plugin")?>"
                type="text" class="regular-text ltr" value="<?= $password;?>">
        </label>

        <p class="form-field hint">
            <?= __("The values of these inputs are utilized for all SMS actions.","smr-plugin") ?>
        </p>

        <label>
            <i class='fi api big'></i>
            WSDL API Url:<br/>
            <input id="wsdl_api" name="smr_config_option[sms_panel][wsdl_api]" placeholder="<?= __("WSDL api url" ,"smr-plugin")?>"
                type="text" class="large-text ltr" value="<?= $wsdlApi ?>">
        </label>
        <p class="form-field hint">
            <?= __("This plugin communicates with WSDL api service through PHP soap api,","smr-plugin") ?>
            <?= __("which takes the above input to connect with api.", "smr-plugin") ?> <br>
            <?= __("The SendByBaseNumber2 function is used to transmit parameters,","smr-plugin") ?> <br>
            <?= __("which include username, password, to, bodyId, and text.", "smr-plugin") ?>
        </p>
        <?php
    }

    // ---------------------- checkout -----------------------------
    public function conditionalCheckoutField() {
        $values = get_option('smr_config_option', []);
        $checkout = $values['checkout'] ?? [];
        ?>
        <label class="d-block">
            <input id="active_checkout" type='checkbox' value="checked" name='smr_config_option[checkout][billing_field_active]' <?= $checkout['billing_field_active'] ?>>
            <?= __("Activate checkout field", "smr-plugin") ?>
        </label> <br>
        <textarea id="checkout_msg" name="smr_config_option[checkout][billing_field_message]"
            class="large-text code <?= $this->cssDir ?>" style="height: 120px;" markdown
            placeholder="<?= __("Message for conditional checkout field..","smr-plugin")?>"><?= esc_attr($checkout['billing_field_message'] ?? '');?></textarea>
        <?php
    }
    /**
     * callback function.
     * input for free shipping cities.
     */
    public function freeShippingCitiesInput() {
        $values = get_option('smr_config_option', []);
        $checkout = $values["checkout"] ?? [];
        $freeShippingCities = $checkout['free_ship_cities'] ?? "";

        ?>
        <i class='fi city big'></i>
        <input id='free_ship_cities' name='smr_config_option[checkout][free_ship_cities]'
                type="text" class="regular-text" value="<?= $freeShippingCities;?>" tagged
                placeholder="<?= __("e.g. London, Tehran","smr-plugin")?>">
        <p class="form-field hint">
            <?= __("To input the city name, press Tab/Space/Enter.","smr-plugin") ?>
        </p>
        <?php
    }

    /**
     * callback function.
     * input for cash on delivery cities.
     */
    public function codCitiesInput() {
        $values = get_option('smr_config_option', []);
        $checkout = $values["checkout"] ?? [];
        $codCities = $checkout['cod_cities'] ?? "";

        ?>
        <i class='fi city big'></i>
        <input id="cod_cities" name="smr_config_option[checkout][cod_cities]"
                type="text" class="regular-text" value="<?= $codCities;?>" tagged
                placeholder="<?= __("e.g. London, Tehran","smr-plugin")?>">
        <p class="form-field hint">
            <?= __("To input the city name, press Tab/Space/Enter.","smr-plugin") ?>
        </p>
        <?php
    }

    // ---------------------- contact form -----------------------------
    public function contactFormSmsId() {
        $values = get_option('smr_config_option', []);
        $contactForm = $values['contact_form'] ?? [];
        $id = $contactForm['sms_id'] ?? "";

        ?>
        <label>
            <i class='fi sms'></i>
            SMS ID:<br/>
            <input id="contact_form_sms_id" name="smr_config_option[contact_form][sms_id]"
                pattern="\d+" placeholder=<?= __("sms ID" ,"smr-plugin") ?>
                type="text" class="regular-text <?= $this->cssDir ?>" value="<?= $id ?>">
        </label><br>
        <?php
    }

    public function contactFormBackImage() {
        $values = get_option('smr_config_option', []);
        $contactForm = $values['contact_form'] ?? [];
        $backImage = $contactForm['back_image'];
        $initialImage = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 350'%3E%3Cpolygon points".
                        "='0,35 250,35 280,0 400,0 400,350 0,350' fill='%23555'%3E%3C/polygon%3E%3C/svg%3E";
        ?>
        <img id="selectedImage" src="<?= $backImage ?? $initialImage ?>" height="80px" alt="Form's background image">
        <input id="form_background_image" name="smr_config_option[contact_form][back_image]" style="display: none;"
            type="text" class="regular-text ltr" value="<?= $backImage ?>">
        <?php
    }

    // ---------------------- user actions -----------------------------
    /**
     * callback function.
     * sms id and parameters for after user registration action.
     */
    public function afterRegistrationMessage() {
        $values = get_option('smr_config_option', []);
        $userActionSettings = $values['user_actions'] ?? [];

        $smsID = $userActionSettings['sms_id'] ?? "";
        // $smsParams = $userActionSettings['sms_param'] ?? "";

        ?>
        <label>
            <i class='fi sms'></i>
            SMS ID:<br/>
            <input id="after_reg_msg_id" name="smr_config_option[user_actions][sms_id]"
                pattern="\d+" placeholder="<?= __("sms ID" ,"smr-plugin") ?>"
                type="text" class="regular-text <?= $this->cssDir ?>" value="<?= $smsID;?>">
        </label><br>
        <!-- This line is unuseable for now.
        <label>
            SMS Parameters:<br/>
            <input name="smr_config_option[user_actions][sms_param]"
                placeholder="<= __("SMS parameters separated by a semicolon, for example, XXX;YYY;ZZZ." ,"smr-plugin") ?>"
                type="text" class="regular-text <= $this->cssDir ?>" value="<= $smsParams;?>">
        </label>
        -->
        <p class="form-field hint">
            <?= __("
                Note: To work, this section uses the SMS panel settings section, which delivers input separated by a semicolon.<br/>
                The first input must have the SMS id, and the second must contain external parameters, the first of which is always the username.
            ","smr-plugin") ?>
        </p>
        <?php
    }

    // ---------------------- sticky button -----------------------------
    public function activateStickyButton() {
        $values = get_option('smr_config_option', []);
        $stickyButton = $values['sticky_button'] ?? [];
        $enabled = $stickyButton['enabled'] ? "checked" : "";
        ?>
        <input id="sticky_activate" value="1" type='checkbox' name='smr_config_option[sticky_button][enabled]' <?= $enabled ?>>

        <p class="form-field hint <?= $this->cssDir ?>">
            <?= __('In this area, you may change the text and color of the sticky button, as well as whether the button is enabled or pinned.','smr-plugin') ?> <br>
            <?= __('If you choose the "icon as link" option, links in the textarea are set directly for the button and no text is displayed for that button.','smr-plugin') ?> <br>
            <?= __('To configure links for several platforms, select "desktop, mobile, and tablet" or leave an empty link and select "icon as link." (e.g. [desktop](https://linkForDesktop.com))','smr-plugin') ?>
        </p>
        <?php
    }

    public function stickyButtonPos() {
        $values = get_option('smr_config_option', []);
        $stickyButton = $values['sticky_button'] ?? [];

        $left = $stickyButton["pos"] == "left" ? $stickyButton['xoffset'] : "-1";
        $right = $stickyButton["pos"] == "right" ? $stickyButton['xoffset'] : "-1";
        ?>

        <div class="d-flex ltr" style="justify-content: center; align-items: center; width: fit-content">
            <input id="stickyLeft" class="<?= $left < 1 ? "disable" : "" ?>" min="-1" max="720" type='number' value='<?= $left ?>' name='smr_config_option[sticky_button][left]'>
            <small><i>px</i></small>
            <span class="p-relative" style="margin:0 5px; width: 65px; height: 65px; display: inline-block; border-radius: 50%; background-color:#f35;">
                <i class="icon fi bell"></i>
            </span>
            <input id="stickyRight" class="<?= $right < 1 ? "disable" : "" ?>" min="-1" max="720" type='number' value='<?= $right ?>' name='smr_config_option[sticky_button][right]'>
            <small><i>px</i></small>
        </div>
        <?php
    }

    /**
     * callback function.
     * input for sticky button instagram info.
     */
    public function stickyButtonInstaInfo() {
        $default = '**Follow my work at: [Instagram](https://www.instagram.com/s_m_r76/)**.';
        $this->stickyButton("instagram", $default);
    }

    /**
     * callback function.
     * input sticky button call info.
     */
    public function stickyButtonCallInfo() {
        $default = '**Your call info text goes here (*minimal markdown supported*)**<br>'.
                   '+ phone number 1: [0123 456 7890](tel:+981234567890).<br>'.
                   '+ phone number 2: [0123 456 7890](tel:+981234567890).';
        $this->stickyButton("call", $default);
    }

    /**
     * callback function.
     * input sticky button call info.
     */
    public function stickyButtonWhatsAppInfo() {
        $default = '**Your WhatsApp info text goes here (*minimal markdown supported*)**<br>'.
                   '+ phone number 1: [0123 456 7890](https://wa.me/+981234567890).<br>'.
                   '+ phone number 2: [0123 456 7890](https://wa.me/+981234567890).';
        $this->stickyButton("whatsapp", $default);
    }

    public function stickyButton($name, $default = "") {
        $values = get_option('smr_config_option', []);
        $stickyButton = $values['sticky_button'] ?? [];
        $button = $stickyButton[$name] ?? [];

        $text = $button['text'] ?? "";
        $color = $button['color'] ?? "#ff2d49";
        $pinned = isset($button['pinned']) ? "checked" : "";
        $enabled = isset($button['enabled']) ? "checked" : "";
        $iconAsLink = isset($button['iconAsLink']) ? "checked" : "";

        $text = empty($text) ? $default : $text;

        ?>
        <div class="st-box">
            <span class="p-relative">
                <i class="icon fi <?= $name ?>"></i>
                <input type='color' value="<?= $color ?>" class="st-color" name='smr_config_option[sticky_button][<?= $name ?>][color]'>
            </span>
            <span>
                <label>
                    <input type='checkbox' value="1" name='smr_config_option[sticky_button][<?= $name ?>][pinned]' <?= $pinned ?> class="pin"> pin
                </label>
                <label>
                    <input type='checkbox' value="1" name='smr_config_option[sticky_button][<?= $name ?>][enabled]' <?= $enabled ?>> enable
                </label>
                <label>
                    <input type='checkbox' value="1" name='smr_config_option[sticky_button][<?= $name ?>][iconAsLink]' <?= $iconAsLink ?>> icon as link
                </label>
            </span>
        </div>
        <textarea id="<?= $name ?>_text" name="smr_config_option[sticky_button][<?= $name ?>][text]"
            class="large-text code <?= $this->cssDir ?>" style="height: 120px;" markdown
            placeholder="<?= __("enter sticky $name button text.","smr-plugin")?>"><?= esc_attr($text);?></textarea>
        <?php
    }
}
?>