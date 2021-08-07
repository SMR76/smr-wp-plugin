<?php

/**
 * @package smr plugin
 */
?>

<div class="wrap">
    <h3>General Page</h3>
    <?php
        wp_enqueue_script("simple-autocomplete",$this->pluginUrl.'/assets/js/simple-autocomplete.js',['jquery']);
        
        global $wp_roles;
        
        settings_errors();         
        $allRoleNames = $wp_roles->get_names();
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
        jQuery(document).ready(() => {
            let source = ["<?php echo implode('","',array_keys($allRoleNames)) ?>"];
            var autoComplete = new autoCompleter(source,"#suggestionListContainer","#ws-selected-roles");
        });

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