<?php

// If this file is called directly, abort.
if ( ! defined('WPINC')) {
    die;
}

?><h2>Edit <?php echo $data['plugin']->name; ?></h2>
<hr>
<h3>
    <i class="fa <?php echo getHostIcon($data['plugin']->host); ?>"></i>&nbsp;
    <a href="<?php echo getHostBaseUrl($data['plugin']->host) . $data['plugin']->repository; ?>" target="_blank">
        <?php echo $data['plugin']->repository; ?>
    </a>
</h3>

<br>

<form action="" method="POST">
    <input type="hidden" name="wppusher[action]" value="edit-plugin">
    <input type="hidden" name="wppusher[file]" value="<?php echo $data['plugin']->file; ?>">
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row">
                <label>Plugin repository</label>
            </th>
            <td>
                <input class="regular-text" type="text" name="wppusher[repository]" value="<?php echo $data['plugin']->repository; ?>">
                <p class="description">Example: wppusher/awesome-wordpress-theme</p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label>Repository branch</label>
            </th>
            <td>
                <input placeholder="master" type="text" name="wppusher[branch]" value="<?php echo $data['plugin']->repository->getBranch(); ?>">
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label>Repository subdirectory</label>
            </th>
            <td>
                <input name="wppusher[subdirectory]" type="text" class="regular-text" placeholder="Optional" value="<?php echo $data['plugin']->getSubdirectory(); ?>">
                <p class="description">Only relevant if your plugin resides in a subdirectory of the repository.</p>
                <p class="description">Example: <strong>awesome-plugin</strong> or <strong>plugins/awesome-plugin</strong></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label></label>
            </th>
            <td>
                <label><input type="checkbox" name="wppusher[ptd]" <?php echo ($data['plugin']->pushToDeploy) ? 'checked' : null; ?>> Push-to-Deploy</label>
            </td>
        </tr>
        </tbody>
    </table>
    <br>
    <input value="Save changes" type="submit" class="button button-primary">
</form>
<br><br>
<form action="" method="POST">
    <input type="hidden" name="wppusher[action]" value="unlink-plugin">
    <input type="hidden" name="wppusher[file]" value="<?php echo $data['plugin']->file; ?>">
    <input type="submit" class="button button-delete" value="Unlink plugin" style="float:right;">
</form>
<a href="?page=wppusher-plugins">Back to plugins</a>
<br><br>
