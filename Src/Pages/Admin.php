<?php
/**
 * @package smr plugin
 */


namespace Src\Pages;

use \Src\Base\BaseController;

class Admin extends BaseController{
    public $functions;

    function __construct() {
        parent::__construct();
        $this->functions = new AdminFunctions();
    }

    public function register() {
        add_action('admin_menu', [$this, 'setupSettingPage']);
        add_action('admin_init', [$this, 'registerCustomFields']);
    }

    public function setupSettingPage() {
        add_menu_page('SMR plugin', 'SMR plugin', 'manage_options', 'smr_general_page', [$this->functions,'adminGeneralPage'],'dashicons-store',100);
        add_submenu_page('smr_general_page', 'SMR plugin', 'General', 'manage_options', 'smr_general_page');
    }

    public function registerCustomFields() {
        register_setting(
            'smr_option_group',
            'smr_config_option',
            [$this->functions, 'optionGroupFieldsFilter']
        );

        /**
         * settings sections:
         * @li Wholesale users.
         * @li Sms Panel options.
         * @li Checkout.
         * @li User actions.
         * @li Sticky button.
         */
        $this->wholesaleSectionFields();
        $this->smsPanelOptionsField();
        $this->checkoutSectionFields();
        $this->contactFormSectionFields();
        $this->userActionsSectionFields();
        $this->stickyButtonSectionFields();
    }

    /**
     * wholesale section fields.
     */
    private function wholesaleSectionFields() {
        add_settings_section(
            'smr_wholesale_section',
            __('Wholesale Settings','smr-plugin'),
            [$this->functions, 'wholesaleSection'],
            'smr_general_page'
        );

        add_settings_field(
            'ws_activate',
            __('activate wholesale ','smr-plugin'),
            [$this->functions, 'activateWholesale'],
            'smr_general_page',
            'smr_wholesale_section',
            ['label_for' => 'ws_activate']
        );

        add_settings_field(
            'ws_roles',
            __('wholesale default roles','smr-plugin'),
            [$this->functions, 'wholesaleRolesInput'],
            'smr_general_page',
            'smr_wholesale_section',
            ['label_for' => 'ws_roles']
        );
    }

    /**
     * plugin options section fields.
     */
    private function smsPanelOptionsField() {
        add_settings_section(
            'smr_sms_panel_section',
            '<hr>'.__('SMS panel settings','smr-plugin'),
            [$this->functions, 'smsPanelOptionSection'],
            'smr_general_page'
        );

        add_settings_field(
            'sms_panel',
            __('SMS panel settings','smr-plugin'),
            [$this->functions, 'smsPanelSettings'],
            'smr_general_page',
            'smr_sms_panel_section',
            ['label_for' => 'sms_panel']
        );
    }

    /**
     * checkout section fields.
     * @li free shipping.
     * @li cash on delivery.
     */
    private function checkoutSectionFields() {
        add_settings_section(
            'smr_checkout_section',
            '<hr>'.__('Checkout Settings','smr-plugin'),
            [$this->functions, 'checkoutSection'],
            'smr_general_page'
        );

        add_settings_field(
            'activate_checkout',
            __('Activate the conditional checkout field with a personalized message.','smr-plugin'),
            [$this->functions, 'conditionalCheckoutField'],
            'smr_general_page',
            'smr_checkout_section',
            ['label_for' => 'activate_checkout']
        );

        add_settings_field(
            'free_ship_cities',
            __('Free shipping cities','smr-plugin'),
            [$this->functions, 'freeShippingCitiesInput'],
            'smr_general_page',
            'smr_checkout_section',
            ['label_for' => 'free_ship_cities']
        );

        add_settings_field(
            'cod_cities',
            __('Cash on delivery cities','smr-plugin'),
            [$this->functions, 'codCitiesInput'],
            'smr_general_page',
            'smr_checkout_section',
            ['label_for' => 'cod_cities']
        );
    }

    /**
     * user actions section fields.
     * @li SMS message after user register.
     * @li Form background image.
     */
    private function contactFormSectionFields() {
        add_settings_section(
            'smr_contact_form_section',
            '<hr>'.__('Contact form','smr-plugin'),
            [$this->functions, 'contactFormSection'],
            'smr_general_page'
        );

        add_settings_field(
            'contact_form_sms_id',
            __('Sends SMS message after contact form submition','smr-plugin'),
            [$this->functions, 'contactFormSmsId'],
            'smr_general_page',
            'smr_contact_form_section',
            ['label_for' => 'contact_form_sms_id']
        );

        add_settings_field(
            'form_background_image',
            __("Select form's background-image",'smr-plugin'),
            [$this->functions, 'contactFormBackImage'],
            'smr_general_page',
            'smr_contact_form_section',
            ['label_for' => 'form_background_image']
        );
    }

    /**
     * user actions section fields.
     * @li SMS message after user register.
     */
    private function userActionsSectionFields() {
        add_settings_section(
            'smr_user_actions_section',
            '<hr>'.__('User actions','smr-plugin'),
            [$this->functions, 'userActionsSection'],
            'smr_general_page'
        );

        add_settings_field(
            'after_reg_msg_id',
            __('send SMS message after user registration','smr-plugin'),
            [$this->functions, 'afterRegistrationMessage'],
            'smr_general_page',
            'smr_user_actions_section',
            ['label_for' => 'after_reg_msg_id']
        );
    }

    /**
     * sticky button section
     * @li stickybutton instagram icon text.
     * @li stickybutton call icon text.
     * @li stickybutton whatsapp icon text.
     * @li SMS panel username and password.
     */
    private function stickyButtonSectionFields() {
        add_settings_section(
            'smr_sticky_btn_section',
            '<hr>'.__('Sticky button settings','smr-plugin'),
            [$this->functions, 'stickyButtonSettings'],
            'smr_general_page'
        );

        add_settings_field(
            'activate_stickybutton',
            __('show stickybutton','smr-plugin'),
            [$this->functions, 'activateStickyButton'],
            'smr_general_page',
            'smr_sticky_btn_section',
            ['label_for' => 'activate_stickybutton']
        );

        add_settings_field(
            'sticky_pos',
            __('set stickybutton position','smr-plugin'),
            [$this->functions, 'stickyButtonPos'],
            'smr_general_page',
            'smr_sticky_btn_section'
        );

        add_settings_field(
            'insta_text',
            __('set sticky button instagram info','smr-plugin'),
            [$this->functions, 'stickyButtonInstaInfo'],
            'smr_general_page',
            'smr_sticky_btn_section',
            ['label_for' => 'insta_text']
        );

        add_settings_field(
            'call_text',
            __('set sticky button call info','smr-plugin'),
            [$this->functions, 'stickyButtonCallInfo'],
            'smr_general_page',
            'smr_sticky_btn_section',
            ['label_for' => 'call_text']
        );

        add_settings_field(
            'whatsapp_text',
            __('set sticky button whatsapp info','smr-plugin'),
            [$this->functions, 'stickyButtonWhatsAppInfo'],
            'smr_general_page',
            'smr_sticky_btn_section',
            ['label_for' => 'whatsapp_text']
        );
    }
}