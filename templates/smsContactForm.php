<style>
    input[type="hidden"] {
        display: none;
        visibility: hidden;
        opacity: 0;
    }

    div.alert.alert-success {
        color: #155724;
        background-color: #d4edda;
        padding: 0.75rem 1.25rem;
        border-radius: 0.25rem;
        border: #c3e6cb solid 1px;
    }

    div.alert.alert-error {
        color: #721c24;
        background-color: #f8d7da;
        padding: 0.75rem 1.25rem;
        border-radius: 0.25rem;
        border: #f5c6cb solid 1px;
    }

    form label {
        cursor: pointer;
    }

    .mb-1 {
        margin-bottom: 5px;
    }
</style>
<?php 
    $currentUser = wp_get_current_user();
    $phoneNumber = $currentUser->get('billing_phone') ?? "";
    $name = $currentUser->get('first_name') . " " . $currentUser->get('last_name');
?>

<form id="request-call" action="" method="post" class="row" style="align-items: flex-end; justify-content: center;">
    <p class="col-12">
        <?php _e("Enter your phone number, and we'll call you as soon as we can.", 'smr-plugin'); ?>
    </p>
    <span id="spinner"></span>
    <p class="col-12 col-sm-12 col-md-5">
        <label for="name">
            <?php _e("First and last name:", 'smr-plugin'); ?>
            <span style="color:red">*</span>
        </label>
        <input id="name" class="input-text" type="text" name="name" placeholder="<?php _e('i.e. Reza Eskandari', 'smr-plugin'); ?>"
               value="<?php echo $name; ?>"
               pattern="\p{L}{2}(\p{L}|\s)*\p{L}" title="<?php _e("At least three letters, including spaces","smr-plugin") ?>" required>
    </p>
    <p class="col-12 col-sm-8 col-md-5">
        <label for="phone">
            <?php _e("Phone number:", 'smr-plugin'); ?>
            <span style="color:red">*</span>
        </label>
        <input class="input-text" type="text" name="phone" id="phone" placeholder="<?php _e('i.e. +989123456789, 09123456789', 'smr-plugin'); ?>"
               value="<?php echo $phoneNumber; ?>" style="direction: ltr;"
               pattern="^(\+98|0)?\d{10}$" title="<?php _e('Enter a 10-digit number starting with +98 or 0.', 'smr-plugin'); ?>" required>
    </p>
    <p class="col-12 col-sm-4 col-md-2">
        <button id="submit" type="submit" class="button" name="track" value="request_call">
            <?php _e('Request for call', 'smr-plugin'); ?>
        </button>
    </p>
    <!-- wordpress nounce -->
    <input type="hidden" name="security" value="<?php echo wp_create_nonce("request_call_nonce") ?>">
    <input type="hidden" name="action" value="request_call">
    <!-- referral url -->
    <input type="hidden" name="referralUrl" value="<?php echo admin_url("admin-ajax.php"); ?>">
</form>