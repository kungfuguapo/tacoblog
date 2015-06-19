<?php

// If this file is called directly, abort.
if ( ! defined('WPINC')) {
    die;
}

?><form method="post" action="<?php echo admin_url(); ?>options.php">
    <?php settings_fields('pusher-license-settings'); ?>
    <?php do_settings_sections('pusher-license-settings'); ?>
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row">
                <label>License</label>
            </th>
            <td>
            <?php if ($data['license_key']) { ?>
                <p>Your license key has <strong><?php echo $data['license_key']->licenses(); ?></strong> site licenses. <strong><?php echo $data['license_key']->usedLicenses(); ?></strong> of them are in use.</p>
                <p>License key is valid until <strong><?php echo date('jS \of F Y', strtotime($data['license_key']->validUntil())); ?></strong> and will <i><a href="https://wppusher.com/faq#renewals"><?php echo ($data['license_key']->autoRenew()) ? 'automatically be renewed' : '<strong>not</strong> be automatically renewed'; ?></a></i>.</p>
            <?php } else { ?>
                <p><i>You haven't registered any license key for this installation. <strong><a href="https://wppusher.com#licenses">Buy one here</a>.</i></strong></p>
            <?php } ?>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label>License key</label>
            </th>
            <td>
                <input name="wppusher_license_key" type="text" id="wppusher_license_key" value="<?php echo get_option('wppusher_license_key'); ?>" class="regular-text" <?php echo (get_option('wppusher_license_key')) ? 'disabled' : null; ?>>
                &nbsp; <input type="submit" name="submit" id="submit" class="button" value="<?php echo (get_option('wppusher_license_key')) ? 'Revoke license from site' : 'Activate site license'; ?>">
            </td>
        </tr>
        </tbody>
    </table>
</form>
<br>
