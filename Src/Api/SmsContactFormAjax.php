<?php
/**
 * @package smr plugin
 */

namespace Src\Api;

use \Src\Base\BaseController;

/**
 * Class SMS Contact Form Ajax
 */
class SmsContactFormAjax extends BaseController {
    public function register() {
        add_action('wp_ajax_nopriv_sms_contact', [$this, 'smsContactFormAjax']); // for non-logged in users
        add_action('wp_ajax_sms_contact', [$this, 'smsContactFormAjax']); // for logged in users
        
        add_action('wp_ajax_remove_sms_cform_number', [$this, 'removeSmsContactNumberAjax']); // for logged in users

        add_shortcode('sms-contact-form', [$this, 'shortCodeCallBack']); // add shortcode
    }

    /**
     * @method shortCodeCallBack
     * Create a shortcode for sms contact form.
     */
    public function shortCodeCallBack() {
        wp_enqueue_script('smsContactForm', $this->pluginUrl.'assets/js/sms-contact-form.js');
        ob_start();
        require_once("$this->pluginPath/templates/smsContactForm.php"); 
        $shortcodeContent = ob_get_contents();
        ob_clean();
        return $shortcodeContent;
    }

    /**
     * @method removeSmsContactNumberAjax
     * admin page ajax
     */
    public function removeSmsContactNumberAjax() {
        check_ajax_referer('remove_sms_cform_number_nonce', 'security'); // check nonce

        $phoneNumbers = $_POST['phoneNumbers'] ?? []; // get phone numbers
        $clearAll = $_POST['clearAll'] ?? false;

        if($clearAll == true) {
            update_option("smr_call_list", []);

            echo json_encode([
                "message" => __("Everythings cleared.", "smr-plugin"),
                "status" => "success"
            ]);
        } else if(isset($phoneNumbers)) {
            $callList = get_option("smr_call_list", []);
            foreach($phoneNumbers as $phoneNumber) {
                unset($callList[$phoneNumber]);
            }
            update_option("smr_call_list", $callList);
            
            echo json_encode([
                "message" => __("Phone number removed.", "smr-plugin"),
                "status" => "success"
            ]);
        } else {
            echo json_encode([
                "message" => __("Invalid inputs.", "smr-plugin"),
                "status" => "error"
            ]);
        }

        wp_die();
    }

    /**
     * @method smsContactFormAjax
     * sms contact form ajax
     */
    public function smsContactFormAjax() {
        check_ajax_referer('sms_contact_nonce', 'security'); // check nonce

        $name = esc_attr($_POST["name"] ?? "");
        $phoneNumber = $_POST["phone"] ?? "";

        define('WP_DEBUG', true);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $phoneNumber = preg_replace("/^0/", "+98", $phoneNumber); // convert 0915... to +98915...
        if (!preg_match("/^\+98\d{10}$/", $phoneNumber) || !preg_match("/^\p{L}{2}(\p{L}|\s)*\p{L}$/", $name)) { // check phone number and name.
            echo json_encode([
                "message" => __("The inputs are invalid.", "smr-plugin"),
                "status" => "error"
            ]);

            nocache_headers(); // disable cache
            status_header(400, 'bad request'); // set status code

            wp_die();
        }

        $callList = get_option("smr_call_list", []); // get list of sms contact numbers

        if(isset($callList[$phoneNumber])) { 
            echo json_encode([
                'message' => __("Your phone number is already in our system.", "smr-plugin"),
                'status' => "error"
            ]);

            nocache_headers();
            status_header(429, 'too many request'); 

            wp_die();
        }

        $callList[$phoneNumber] = [$name, time()];
        update_option("smr_call_list", $callList);
        
        $optionGroup = get_option("smr_config_option", []);

        $smsPanel = $optionGroup["sms_panel"] ?? []; // sms panel
        $smsUsername = $smsPanel["sms_username"] ?? "";
        $smsPassword = $smsPanel["sms_password"] ?? "";
        $wsdlApi = $smsPanel["wsdl_api"] ?? "";
        
        $contactForm = $optionGroup["contact_form"]; // contact form
        $smsID = $contactForm["sms_id"] ?? "";

        if($smsUsername && $smsPassword && $smsID) { // if sms settings are set
            // turn off the WSDL cache
            ini_set("soap.wsdl_cache_enabled", "0");
            $smsClient = new \SoapClient($wsdlApi, ['encoding'=>'UTF-8']);
    
            $parameters['username'] = $smsUsername; // username
            $parameters['password'] = $smsPassword; // SMS panel password
            $parameters['to'] = $phoneNumber;
            $parameters['bodyId'] = $smsID; // sms id
            $parameters['text'] = $name;
    
            $smsClient->SendByBaseNumber2($parameters)->SendByBaseNumber2Result; // send sms
        }

        echo json_encode([
            'message' => __("We have your phone number and will call you as soon as possible.", "smr-plugin"), 
            'status' => "success"
        ]);
        wp_die();
    }
}