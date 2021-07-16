<div class="wrap">
    <h3>General Page</h3>
    <?php settings_errors(); ?>

    <form method="post" action="options.php">
        <?php
            settings_fields('smr_option_group');
            do_settings_sections('smr_plugin');
            submit_button();
        ?>
    </form>
</div>