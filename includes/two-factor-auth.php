<?php

// Include the Google Authenticator library.
require_once plugin_dir_path(dirname(__FILE__)) . 'vendor/autoload.php';

function add_2fa_user_field($user) {
    $secret = esc_attr(get_user_meta($user->ID, '2fa_secret', true));

    if (empty($secret)) {
        $ga = new PHPGangsta_GoogleAuthenticator();
        $secret = $ga->createSecret(); // Generate a new secret if one doesn't exist
    }

    $qrCodeUrl = (new PHPGangsta_GoogleAuthenticator())->getQRCodeGoogleUrl(
        get_bloginfo('name'),
        $secret
    );

    ?>
    <h3>Two-Factor Authentication</h3>
    <table class="form-table">
        <tr>
            <th><label for="2fa_secret">2FA Secret</label></th>
            <td>
                <input type="text" name="2fa_secret" id="2fa_secret" 
                       value="<?php echo $secret; ?>" readonly class="regular-text" />
                <p class="description">
                    Scan this QR code with your Google Authenticator app:<br>
                    <img src="<?php echo esc_url($qrCodeUrl); ?>" alt="QR Code" /><br>
                    Or manually enter this code: <strong><?php echo $secret; ?></strong>
                </p>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'add_2fa_user_field');
add_action('edit_user_profile', 'add_2fa_user_field');


// Save the 2FA secret to the user meta
function save_2fa_user_field($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return;
    }

    $secret = sanitize_text_field($_POST['2fa_secret']);
    update_user_meta($user_id, '2fa_secret', $secret);
}
add_action('personal_options_update', 'save_2fa_user_field');
add_action('edit_user_profile_update', 'save_2fa_user_field');

function verify_2fa_on_login($user, $username, $password) {
    $user_data = get_user_by('login', $username);

    if (!$user_data) {
        return $user;
    }

    $ga = new PHPGangsta_GoogleAuthenticator();
    $secret = get_user_meta($user_data->ID, '2fa_secret', true);
    echo $secret;
    $code = isset($_POST['2fa_code']) ? sanitize_text_field($_POST['2fa_code']) : '';

    echo $code;
    if (!$secret) {
        // If no secret exists, skip 2FA
        return $user;
    }

    if (empty($code)) {
        return new WP_Error('missing_2fa', __('The Two-Factor Authentication code is required.'));
    }

    
    if ($secret!==$code) { // '2' is the time window in seconds
       var_dump($ga);
        return new WP_Error('invalid_2fa', __('Invalid Two-Factor Authentication code. ' . $error_message));
    }

    return $user;
}
add_filter('authenticate', 'verify_2fa_on_login', 30, 3);

function add_2fa_login_field() {
    ?>
    <p>
        <label for="2fa_code"><?php _e('Two-Factor Authentication Code'); ?></label>
        <input type="text" name="2fa_code" id="2fa_code" class="input" value="" size="20" />
    </p>
    <?php
}
add_action('login_form', 'add_2fa_login_field');

