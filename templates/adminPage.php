<?php

/**
 * @package smr plugin
 */
?>

<div class="wrap">
    <h3>General Page</h3>
    <?php
        global $wp_roles;
        
        settings_errors();         
        $allRoleNames = $wp_roles->get_names();
    ?>

    <form method="post" action="options.php">
        <?php
            settings_fields('smr_ws_option_group');
            do_settings_sections('smr_plugin');
            submit_button();
        ?>
    </form>

    
    <script>
        jQuery(function($){
            jQuery('input#qty_args').click(function(){
                if( jQuery(this).is(':checked')) {
                    jQuery('div.qty-args').removeClass('hidden');
                } else {
                    jQuery('div.qty-args').addClass('hidden');
                }
            });

            var source=["<?php echo implode('","',array_keys($allRoleNames)) ?>"];
            
            var suggestionClicked = function() {
                let suggest = jQuery(this).html();
                let input   = jQuery('#ws-selected-roles');
                let value   = input.val();
                let lastIndex   = value.lastIndexOf(',');
                let newInput    = lastIndex == -1? '' : value.substr(0,lastIndex) + ',';

                input.val(newInput + suggest);
                jQuery("#suggestionListContainer").html('');
            };

            jQuery('#ws-selected-roles').keyup(function() {
                let rolesInput  = jQuery(this).val().toLocaleLowerCase().replaceAll(' ','');
                let lastIndex   = rolesInput.lastIndexOf(',');
                let result      = []; 

                rolesInput = lastIndex == -1 ? rolesInput : rolesInput.substr(lastIndex + 1);
                for(let x of source) {
                    if(rolesInput != '' && x.toLocaleLowerCase().includes(rolesInput) == true) {
                        result.push(`<a class="btn suggestedItem">${x}</a>`);
                    }
                }
                jQuery("#suggestionListContainer").html(result.join(', '));

                if(result.length)
                    jQuery('.suggestedItem').click(suggestionClicked);
            });
        });
    </script>
</div>