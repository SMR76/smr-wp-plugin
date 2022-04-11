<?php

/**
 * @package smr plugin
 */

?>
<div class="wrap">
    <h3>General Page</h3>

    <?php
    wp_enqueue_script("simple-autocomplete", $this->pluginUrl . '/assets/js/simple-autocomplete.js', ['jquery']);
    wp_enqueue_script("multiselect-dropdown", $this->pluginUrl . '/assets/js/multiselect-dropdown.js');
    wp_enqueue_script("simple-tag-input", $this->pluginUrl . '/assets/js/simple-tag-input.js');
    wp_enqueue_script("simple-markdown", $this->pluginUrl . '/assets/js/simple-markdown.js');
    wp_enqueue_script("admin-page", $this->pluginUrl . '/assets/js/admin-page.js');

    wp_enqueue_style("simple-tag-input", $this->pluginUrl . '/assets/css/simple-tag-input.css');
    wp_enqueue_style("simple-markdown", $this->pluginUrl . '/assets/css/simple-markdown.css');
    wp_enqueue_style("bootstrap-grid", $this->pluginUrl . '/assets/css/bootstrap-grid.css');
    wp_enqueue_style("admin-page", $this->pluginUrl . '/assets/css/admin-page.css');
    ?>

    <style>
        /* form hint style */
        .form-field.hint { color: darkcyan; height: min-content; max-width: max-content; direction: <?= is_rtl() ? "rtl" : "ltr" ?>; }
        input[type="text"] { margin-top: 5px; }
    </style>

    <div class="tab-view">
        <ul class="tab-header">
            <li class="active" tab-page="settings">settings</li>
            <li class="" tab-page="sms-contact-list">sms contact numbers</li>
            <li class="" tab-page="developer">developer</li>
        </ul>

        <?php settings_errors(); ?>

        <div class="tab-body">
            <div id="settings" class="tab-page active">
                <form id="general" method="post" action="options.php">
                    <?php
                    settings_fields('smr_option_group');
                    do_settings_sections('smr_general_page');
                    submit_button();
                    ?>
                </form>
            </div>
            <div id="sms-contact-list" class="tab-page container-fulid grid">
                <div class="row head-row" style="flex-wrap: nowrap; justify-content: space-between; margin: 0; padding: 0 15px;">
                    <div dir="ltr">short-code: 
                        <code onclick="navigator.clipboard.writeText(this.innerHTML)">sms-contact-form</code>
                        <i>copied!</i>
                    </div>
                    <input id="clearAll" type="button" value="Clear All">
                </div>
                <div class="row">
                    <div class="col-2 text-center">#</div>
                    <div class="col-8 col-md-9 text-center">
                        <div class="row">
                            <div class="col-12 col-sm-6 text-center bold"><?= __("Name", "smr-plugin") ?></div>
                            <div class="col-12 col-sm-6 text-center bold"><?= __("Phone Number", "smr-plugin") ?></div>
                        </div>
                    </div>
                </div>

                <?php
                global $wpdb;

                $index = 0;
                $callList = get_option('smr_call_list', []);
                $usersdb = $wpdb->get_results("SELECT user_id, meta_value as phone_number FROM `$wpdb->usermeta` WHERE meta_key = 'billing_phone' AND meta_value != ''");

                foreach ($callList as $phoneNumber => $info) {
                    $index++;
                    $userId = "";
                    $userURL = "";
                    $name = $info[0];
                    $time = $info[1];
                    $inPhoneNumber = "+98" . substr($phoneNumber, -10);

                    foreach ($usersdb as $user) {
                        if (substr($user->phone_number, -10) == substr($phoneNumber, -10)) {
                            $userId = $user->user_id;
                            $userURL = "href='" . get_edit_profile_url($userId) . "'";
                            break;
                        }
                    }

                    $alt = is_null($userId) ? "user not found" : "";

                    echo "<div class='row'>";
                    echo "    <div class='col-2 text-center'>$index</div>";
                    echo "    <div class='col-8 col-md-9 text-center'>";
                    echo "        <div class='row'>";
                    echo "            <div class='col-12 col-sm-6 text-center'><a $userURL alt='$alt'>$name</a></div>";
                    echo "            <div class='col-12 col-sm-6 text-center ltr'><a href='tel:$inPhoneNumber'>$phoneNumber</a></div>";
                    echo "        </div>";
                    echo "    </div>";
                    echo "    <div class='col-2 col-md-1 text-center'>";
                    echo "        <button phone-number='$phoneNumber'></button>";
                    echo "    </div>";
                    echo "</div>";
                }
                ?>
            </div>
            
            <div id="developer" class="tab-page">
                <div class="container-fulid" dir="ltr">
                    <div class="row" style="padding: 5px;">
                        <div class="col-12">
                            <h2>
                                <?= __("Plugin configuration raw data", "sms-plugin") ?>
                            </h2>
                        </div>
                        <div class="col-12">
                            <?php
                                var_dump(get_option('smr_config_option', []));
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- wordpress nounce -->
    <input id="security" type="hidden" name="security" value="<?= wp_create_nonce("remove_sms_cform_number_nonce") ?>">
    <input id="action" type="hidden" name="action" value="remove_sms_cform_number">
    <!-- referral url -->
    <input id="referralUrl" type="hidden" name="referralUrl" value="<?= admin_url("admin-ajax.php"); ?>">
</div>