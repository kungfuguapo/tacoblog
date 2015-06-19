<?php

// If this file is called directly, abort.
if ( ! defined('WPINC')) {
    die;
}

?><h2>Edit <?php echo $data['theme']->name; ?></h2>
<hr>
<h3>
    <i class="fa <?php echo getHostIcon($data['theme']->host); ?>"></i>&nbsp;
    <a href="<?php echo getHostBaseUrl($data['theme']->host) . $data['theme']->repository; ?>" target="_blank">
        <?php echo $data['theme']->repository; ?>
    </a>
</h3>

<br>

<form action="" method="POST">
    <input type="hidden" name="wppusher[action]" value="edit-theme">
    <input type="hidden" name="wppusher[stylesheet]" value="<?php echo $data['theme']->stylesheet; ?>">
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row">
                <label>Theme repository</label>
            </th>
            <td>
                <input class="regular-text" type="text" name="wppusher[repository]" value="<?php echo $data['theme']->repository; ?>">
                <p class="description">Example: wppusher/awesome-wordpress-theme</p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label>Repository branch</label>
            </th>
            <td>
                <input placeholder="master" type="text" name="wppusher[branch]" value="<?php echo $data['theme']->repository->getBranch(); ?>">
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label>Repository subdirectory</label>
            </th>
            <td>
                <input name="wppusher[subdirectory]" type="text" class="regular-text" placeholder="Optional" value="<?php echo $data['theme']->getSubdirectory(); ?>">
                <p class="description">Only relevant if your theme resides in a subdirectory of the repository.</p>
                <p class="description">Example: <strong>awesome-theme</strong> or <strong>plugins/awesome-theme</strong></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label></label>
            </th>
            <td>
                <label><input type="checkbox" name="wppusher[ptd]" <?php echo ($data['theme']->pushToDeploy) ? 'checked' : null; ?>> Push-to-Deploy</label>
            </td>
        </tr>
        </tbody>
    </table>
    <br>
    <input value="Save changes" type="submit" class="button button-primary">
</form>
<br><br>
<form action="" method="POST">
    <input type="hidden" name="wppusher[action]" value="unlink-theme">
    <input type="hidden" name="wppusher[stylesheet]" value="<?php echo $data['theme']->stylesheet; ?>">
    <input type="submit" class="button button-delete" value="Unlink theme" style="float:right;">
</form>
<a href="?page=wppusher-themes">Back to themes</a>
<br><br>
