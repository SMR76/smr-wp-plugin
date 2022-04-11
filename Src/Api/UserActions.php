<?php
/**
 * @package smr plugin
 */

namespace Src\Api;

use \Src\Base\BaseController;

/**
 * This class includes various customized user action handlers.
 */
class UserActions extends BaseController {
    public function register() {
        add_action("user_register", [$this, "afterUserRegister"], 10, 2);
    }

    /**
     * @method afterUserRegister
     * @param int $userId
     * @param array $userData
     * @brief after user register
     * This action method will be called once the user registers on the website.
     * It is expected to send a custom SMS to the user's phone number.
     */
    public function afterUserRegister($userId, $userData) {
        $username = $userData["user_login"];
        $phoneNumber = get_user_meta($userId, "phone_number", true);

        $optionGroup = get_option("smr_config_option", []); 

        // initial sms panel settings.
        $smsPanel = $optionGroup["sms_panel"] ?? [];
        $smsUsername = $smsPanel["sms_username"] ?? "";
        $smsPassword = $smsPanel["sms_password"] ?? "";
        $wsdlApi = $smsPanel["wsdl_api"] ?? "";
        
        $contactForm = $optionGroup["contact_form"]; // contact form
        $smsID = $contactForm["sms_id"] ?? "";
        
        if($smsUsername && $smsPassword && $smsID && $username && $phoneNumber) { // if sms settings are set
            ini_set("soap.wsdl_cache_enabled", "0"); // turn off the WSDL cache
            $smsClient = new \SoapClient($wsdlApi, ['encoding'=>'UTF-8']);
    
            $parameters['username'] = $smsUsername; // username
            $parameters['password'] = $smsPassword; // SMS panel password
            $parameters['to'] = $phoneNumber;
            $parameters['bodyId'] = $smsID; // sms id
            $parameters['text'] = "$username;".$optionGroup["sms_param"]; 
    
            $smsClient->SendByBaseNumber2($parameters)->SendByBaseNumber2Result; // send sms
        }
    }
} 