<?php

// If this file is called directly, abort.
if ( ! defined('WPINC')) {
    die;
}

?><h2>
    <img src="https://wppusher.com/moerkeblaa_002.png" width="250">
</h2>

<?php if ( ! get_option('hide-wppusher-welcome', false)) { ?>
<div id="welcome-panel" class="welcome-panel">
    <a class="welcome-panel-close" href="?page=wppusher&wppusher-welcome=0">Dismiss</a>
    <div class="welcome-panel-content">
        <h3>Thanks for installing WP Pusher!</h3>
        <p class="about-description">Here's how to get started:</p>
        <div class="welcome-panel-column-container">
            <div class="welcome-panel-column">
                <h4>Using Private Repositories</h4>
                <a class="button button-primary button-hero" href="https://wppusher.com/#licenses">Buy A License</a>
                <p>or, type in your license key in the form below</p>
            </div>
            <div class="welcome-panel-column">
                <h4>Next Steps</h4>
                <ul>
                    <li><a href="#wppusher-settings" class="welcome-icon welcome-add-page">Add your GitHub, Bitbucket or GitLab credentials</a></li>
                    <li><a href="?page=wppusher-plugins-create" class="welcome-icon welcome-add-page">Install a plugin</a></li>
                    <li><a href="?page=wppusher-themes-create" class="welcome-icon welcome-add-page">Install a theme</a></li>
                </ul>
            </div>
            <div class="welcome-panel-column welcome-panel-last">
                <h4>More Actions</h4>
                <ul>
                    <li><a href="https://github.com/wppusher/wppusher-documentation/blob/master/push-to-deploy.md" target="_blank" class="welcome-icon welcome-learn-more">Learn about Push-to-Deploy</a></li>
                    <li><a href="https://github.com/wppusher/wppusher-documentation" target="_blank" class="welcome-icon welcome-learn-more">Take a look at the docs</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<?php settings_errors('invalid-license-key') ?>
<?php settings_errors('invalid-license-server-message') ?>
<?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') { ?>
    <div class="updated"><p>Settings updated.</p></div>
<?php } ?>

<h2>License</h2>

<hr>

<?php include 'license.php'; ?>

<h2 id="wppusher-settings">Settings</h2>

<hr>

<?php include 'token.php'; ?>

<h3>GitHub</h3>

<form method="post" action="<?php echo admin_url(); ?>options.php">
<?php settings_fields('pusher-gh-settings'); ?>
<?php do_settings_sections('pusher-gh-settings'); ?>
<table class="form-table">
    <tbody>
        <tr>
            <th scope="row">
                <label>GitHub token</label>
            </th>
            <td>
                <input name="gh_token" type="text" id="gh_token"  placeholder="<?php echo (get_option('gh_token')) ? '********' : null; ?>" class="regular-text">
                <p class="description">You only need a token if your repositories are private.</p>
                <p class="description">Learn more about GitHub tokens <a target="_blank" href="https://help.github.com/articles/creating-an-access-token-for-command-line-use/">here</a>.</p>
            </td>
        </tr>
    </tbody>
</table>
<?php submit_button('Save GitHub token'); ?>
</form>

<hr>

<h3>Bitbucket</h3>

<form method="post" action="<?php echo admin_url(); ?>options.php">
<?php settings_fields('pusher-bb-settings'); ?>
<?php do_settings_sections('pusher-bb-settings'); ?>
<table class="form-table">
    <tbody>
        <tr>
            <th scope="row">
                <label>Bitbucket username</label>
            </th>
            <td>
                <input name="bb_user" type="text" id="bb_user" value="<?php echo esc_attr(get_option('bb_user')); ?>" class="regular-text">
                <p class="description">Only neccessary if you have private repositories.</p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label>Bitbucket password</label>
            </th>
            <td>
                <input name="bb_pass" type="password" id="bb_pass" class="regular-text" placeholder="<?php echo (get_option('bb_pass')) ? '********' : null; ?>">
                <p class="description">It is highly recommended that you create a seperate <strong>read only</strong> user for WP Pusher to use.</p>
            </td>
        </tr>
    </tbody>
</table>
<?php submit_button('Save Bitbucket credentials'); ?>
</form>

<hr>

<h3>GitLab</h3>

<form method="post" action="<?php echo admin_url(); ?>options.php">
    <?php settings_fields('pusher-gl-settings'); ?>
    <?php do_settings_sections('pusher-gl-settings'); ?>
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row">
                <label>GitLab base url</label>
            </th>
            <td>
                <input name="gl_base_url" type="text" id="gl_base_url" value="<?php echo esc_attr(get_option('gl_base_url')); ?>" class="regular-text">
                <p class="description">Defaults to 'https://gitlab.com'.</p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label>GitLab private token</label>
            </th>
            <td>
                <input name="gl_private_token" type="text" id="gl_private_token" class="regular-text" placeholder="<?php echo (get_option('gl_private_token')) ? '********' : null; ?>">
                <p class="description">Only neccessary if you have private repositories.</p>
                <p class="description">Find private token in <strong>Settings > Account</strong>.</p>
            </td>
        </tr>
        </tbody>
    </table>
    <?php submit_button('Save GitLab settings'); ?>
</form>

<h3>Logging</h3>

You can enable logging for debugging purposes. Log files can grow quickly, so don't leave this on.

<form method="post" action="<?php echo admin_url(); ?>options.php">
    <?php settings_fields('pusher-enable-logging'); ?>
    <?php do_settings_sections('pusher-enable-logging'); ?>
    <?php if (get_option('pusher_logging_enabled') == 1) { ?>
        <input type="hidden" name="pusher_logging_enabled" value="0">
        <?php submit_button('Disable logging'); ?>
    <?php } else { ?>
        <input type="hidden" name="pusher_logging_enabled" value="1">
        <?php submit_button('Enable logging'); ?>
    <?php } ?>
</form>

<?php if (get_option('pusher_logging_enabled') == 1) { ?>
    <textarea rows="20" style="width: 100%;" disabled><?php echo $data['log']; ?></textarea>
    <form method="post" action="" onsubmit="return confirm('The log is gonna be wiped clean. Sure about it?');">
        <input type="hidden" name="wppusher[action]" value="clear-log">
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Clear log">
        </p>
    </form>
<?php } ?>
