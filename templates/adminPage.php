<?php

/**
 * @package smr plugin
 */
?>
<style>
    .form-table th,
    .form-table td {
        padding: 5px 0px;
    }
</style>

<div class="wrap">
    <h3>General Page</h3>
    <?php
        wp_enqueue_script("simple-autocomplete",$this->pluginUrl.'/assets/js/simple-autocomplete.js',['jquery']);
        wp_enqueue_script("multiselect-dropdown",$this->pluginUrl.'/assets/js/multiselect-dropdown.js');

        wp_enqueue_style("simple-tag-input",$this->pluginUrl.'/assets/css/simple-tag-input.css');
        wp_enqueue_script("simple-tag-input",$this->pluginUrl.'/assets/js/simple-tag-input.js');
                
        settings_errors();         
    ?>

    <form method="post" action="options.php">
        <?php
            settings_fields('smr_option_group');
            do_settings_sections('smr_general_page');
            do_settings_sections('smr_options_activate_section');
            submit_button();
        ?>
    </form>
        
    <script>
        jQuery(function($) {
            if($("#activate_wholesale").is(':checked') == false)
                $("#ws-selected-roles").parent().parent().hide();

            $("#activate_wholesale").click(function() {
                if($(this).is(':checked'))
                    $("#ws-selected-roles").parent().parent().show();
                else
                    $("#ws-selected-roles").parent().parent().hide();
            });
        });
    </script>
</div>