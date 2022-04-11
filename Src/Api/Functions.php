<?php
/**
 * @package smr plugin
 */

namespace Src\Api;

use \Src\Base\BaseController;

class Functions extends BaseController {
    private $cssDirection = "ltr";

    public function __construct() {
        parent::__construct();
        $this->cssDirection = is_rtl() ? "rtl" : "ltr";
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
        return $input;
    }

    public function wholesaleSection() {
        _e('Wholesale users settings','smr-plugin');
    }

    public function smsPanelOptionSection() {
        _e('SMS panel settings','smr-plugin');
    }

    public function checkoutSection() {
        _e('checkout settings.','smr-plugin');
    }

    public function contactFormSection() {
        _e('Contact form settings.','smr-plugin');
    }

    public function userActionsSection() {
        _e('Settings for user actions, such as sending SMS messages following registration.','smr-plugin');
    }

    public function stickyButtonSettings() {
        _e('Sticky button settings.','smr-plugin');
    }

    // ---------------------- wholesale -----------------------------
    /**
     * inputs for wholesale section.
     */
    public function activateWholesale() {
        $values = get_option('smr_config_option');
        $wholesale = $values['wholesale'];        
        ?> 
        <input id="ws_active" type='checkbox' value="checked" name='smr_config_option[wholesale][active]' <?= $wholesale['active'] ?>> 
        <?php
    }
    
    public function wholesaleRolesInput() {
        global $wp_roles;
        $allRoleNames = $wp_roles->get_names();
        
        $values = get_option('smr_config_option');
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
        $values = get_option('smr_config_option');
        $smsPanel = $values['sms_panel'] ?? '';

        $username = $smsPanel['sms_username'] ?? '';
        $password = $smsPanel['sms_password'] ?? '';
        $wsdlApi = $smsPanel['wsdl_api'] ?? '';

        ?>
        <input id="sms_username" name="smr_config_option[sms_panel][sms_username]" style="direction: ltr;"
            placeholder="<?= __("sms username" ,"smr-plugin")?>" 
            type="text" class="regular-text" value="<?= $username;?>">
                
        <input id="sms_password" name="smr_config_option[sms_panel][sms_password]" style="direction: ltr;"
            placeholder="<?= __("sms password" ,"smr-plugin")?>" 
            type="text" class="regular-text" value="<?= $password;?>">
            
        <p class="form-field hint">
            <?= __("The values of these inputs are utilized for all SMS actions.","smr-plugin") ?>
        </p>
        
        <input id="sms_password" name="smr_config_option[sms_panel][wsdl_api]" style="direction: ltr;"
            placeholder="<?= __("WSDL api url" ,"smr-plugin")?>" 
            type="text" class="large-text" value="<?= $wsdlApi ?>">
        <p class="form-field hint">
            <?= __("This plugin communicates with WSDL api service through PHP soap api,
                which takes the above input to connect with api.", "smr-plugin") ?> <br>
            <?= __("The SendByBaseNumber2 function is used to transmit parameters,
                which include username, password, to, bodyId, and text.", "smr-plugin") ?>
        </p>
        <?php
    }

    // ---------------------- checkout -----------------------------
    public function conditionalCheckoutField() {
        $values = get_option('smr_config_option');
        $checkout = $values['checkout'] ?? [];
        ?>
        <input id="active_checkout" type='checkbox' value="checked" name='smr_config_option[checkout][billing_field_active]' <?= $checkout['billing_field_active'] ?>> 
        <textarea id="checkout_msg" name="smr_config_option[checkout][billing_field_message]"
            class="large-text code" style="direction: <?= $this->cssDirection ?>; height: 120px;" markdown
            placeholder="<?= __("Message for conditional checkout field..","smr-plugin")?>"><?= esc_attr($checkout['billing_field_message'] ?? '');?></textarea>
        <?php
    } 
    /**
     * callback function.
     * input for free shipping cities.
     */
    public function freeShippingCitiesInput() {
        $values = get_option('smr_config_option');
        $checkout = $values["checkout"] ?? [];
        $freeShippingCities = $checkout['free_ship_cities'] ?? "";
        
        ?>
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
        $values = get_option('smr_config_option');  
        $checkout = $values["checkout"] ?? [];      
        $codCities = $checkout['cod_cities'] ?? "";
        
        ?>
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
        $values = get_option('smr_config_option');
        $contactForm = $values['contact_form'] ?? [];
        $id = $contactForm['sms_id'] ?? "";

        ?>
        <input id="sms_id" name="smr_config_option[contact_form][sms_id]" style="direction: ltr; width: 80px;"
            pattern="\d+" placeholder=<?= __("sms ID" ,"smr-plugin") ?> 
            type="text" class="regular-text" value="<?= $id;?>">
        <?php
    }

    // ---------------------- user actions -----------------------------
    /**
     * callback function.
     * sms id and parameters for after user registration action.
     */
    public function afterRegistrationMessage() {
        $values = get_option('smr_config_option');
        $userActionSettings = $values['user_actions'] ?? [];
        
        $smsID = $userActionSettings['sms_id'] ?? "";
        $smsParams = $userActionSettings['sms_param'] ?? "";
        
        ?>
        <input id="after_reg_msg_id" name="smr_config_option[user_actions][sms_id]"
            placeholder="<?= __("sms ID" ,"smr-plugin") ?>" style="direction: ltr;width: 80px"
            type="text" class="small-text" value="<?= $smsID;?>">
        <input id="after_reg_msg_param" name="smr_config_option[user_actions][sms_param]" style="direction: ltr;"
            placeholder="<?= __("SMS parameters separated by a semicolon, for example, XXX;YYY;ZZZ." ,"smr-plugin") ?>" 
            type="text" class="regular-text" value="<?= $smsParams;?>">
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
        $values = get_option('smr_config_option');
        $stickyButton = $values['sticky_button'] ?? [];
        ?>
        <input id="sticky_activate" value="checked" type='checkbox' name='smr_config_option[sticky_button][active]' <?= $stickyButton['active'] ?>> 
        <?php
    }

    /**
     * callback function.
     * input for sticky button instagram info.
     */
    public function stickyButtonInstaInfo() {
        $values = get_option('smr_config_option');
        $stickyButton = $values['sticky_button'] ?? [];
        $instaInfo = $stickyButton['insta_info'] ?? "";
        $instaPin = $stickyButton['insta_pinned'] ?? "";

        if(empty($instaInfo)) {
            $instaInfo = '**Follow my work at: [Instagram](https://www.instagram.com/s_m_r76/)**.';
        }
        
        ?>
        <span>pin this button</span>
        <input type='checkbox' value="checked" name='smr_config_option[sticky_button][insta_pinned]' <?= $instaPin ?>> 
        <textarea id="insta_info" name="smr_config_option[sticky_button][insta_info]"
            class="large-text code" style="direction: <?= $this->cssDirection ?>;" markdown
            placeholder="<?= __("enter sticky button instagram info","smr-plugin")?>"><?= esc_attr($instaInfo);?></textarea>
        <?php
    }

    /**
     * callback function.
     * input sticky button call info.
     */
    public function stickyButtonCallInfo() {
        $values = get_option('smr_config_option');
        $stickyButton = $values['sticky_button'] ?? [];
        $callInfo = $stickyButton['call_info'] ?? "";
        $callPin = $stickyButton['call_pinned'] ?? "";
        
        if(empty($callInfo)) {
            $callInfo = 
'**Your call info goes here (*minimal markdown supported*)**
+ phone number 1: [0123 456 7890](tel:+981234567890).
+ phone number 2: [0123 456 7890](tel:+981234567890).';;
        }
        
        ?>
        <span>pin this button</span>
        <input type='checkbox' value="checked" name='smr_config_option[sticky_button][call_pinned]' <?= $callPin ?>>
        <textarea id="call_info" name="smr_config_option[sticky_button][call_info]"
            class="large-text code" style="direction: <?= $this->cssDirection ?>; height: 120px;" markdown
            placeholder="<?= __("enter sticky button calls info.","smr-plugin")?>"><?= esc_attr($callInfo);?></textarea>
        <?php
    }

    /**
     * callback function.
     * input sticky button call info.
     */
    public function stickyButtonWhatsAppInfo() {
        $values = get_option('smr_config_option');
        $stickyButton = $values['sticky_button'] ?? [];
        $whatsAppInfo = $stickyButton['whatsapp_info'] ?? "";
        $whatsAppPin = $stickyButton['whatsapp_pinned'] ?? "";

        if(empty($whatsAppInfo)) {
            $whatsAppInfo = 
'**Your WhatsApp info goes here (*minimal markdown supported*)**
+ phone number 1: [0123 456 7890](https://wa.me/+981234567890).
+ phone number 2: [0123 456 7890](https://wa.me/+981234567890).';
        }
        
        ?>
        <span>pin this button</span>
        <input type='checkbox' value="checked" name='smr_config_option[sticky_button][whatsapp_pinned]' <?= $whatsAppPin ?>>
        <textarea id="whatsapp_info" name="smr_config_option[sticky_button][whatsapp_info]"
            class="large-text code" style="direction: <?= $this->cssDirection ?>; height: 120px;" markdown
            placeholder="<?= __("enter sticky button whatsapp info.","smr-plugin")?>"><?= esc_attr($whatsAppInfo);?></textarea>
        <?php
    }
}
?>