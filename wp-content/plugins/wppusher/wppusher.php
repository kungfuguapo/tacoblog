<?php

/**
 * Plugin Name: WP Pusher
 * Plugin URI: http://wppusher.com
 * Description: Pain-free deployment of WordPress themes and plugins directly from GitHub.
 * Version: 1.1.6
 * Author: WP Pusher
 * Author URI: http://wppusher.com
 * License: GNU GENERAL PUBLIC LICENSE
 */

// If this file is called directly, abort.
if ( ! defined('WPINC')) {
    die;
}

require __DIR__ . '/autoload.php';

use Pusher\Pusher;
use Pusher\PusherServiceProvider;

$pusher = new Pusher;
$pusher->register(new PusherServiceProvider);

register_activation_hook(__FILE__, array($pusher, 'activate'));
// register_deactivation_hook(__FILE__, array($pusher, 'deactivate'));

$pusher->pusherPath = plugin_dir_path(__FILE__);
$pusher->pusherUrl = plugin_dir_url(__FILE__);

require_once('wp-updates-plugin.php');
new WPUpdatesPluginUpdater_957('http://wp-updates.com/api/2/plugin', plugin_basename(__FILE__));

$pusher->init();

if ( ! function_exists('getHostIcon')) {
    function getHostIcon($host)
    {
        if ($host === 'gh') {
            return 'fa-github';
        } elseif ($host === 'bb') {
            return 'fa-bitbucket';
        } else {
            return 'fa-git-square';
        }
    }
}

if ( ! function_exists('getHostBaseUrl')) {
    function getHostBaseUrl($host)
    {
        if ($host === 'gh') {
            return 'https://github.com/';
        } elseif ($host === 'bb') {
            return 'https://bitbucket.org/';
        } elseif ($host === 'gl') {
            return trailingslashit(get_option('gl_base_url'));
        } else {
            return null;
        }
    }
}

$hidePluginsFromUpdateChecks = function($args, $url) use ($pusher)
{
    if (0 !== strpos($url, 'https://api.wordpress.org/plugins/update-check')) {
        return $args;
    }

    $plugins = json_decode($args['body']['plugins'], true);

    $pluginsToHide = array_keys($pusher->plugins->allPusherPlugins());
    $pluginsToHide[] = plugin_basename(__FILE__);

    foreach ($pluginsToHide as $plugin) {
        unset($plugins['plugins'][$plugin]);
        unset($plugins['active'][array_search($plugin, $plugins['active'])]);
    }

    $args['body']['plugins'] = json_encode($plugins);

    return $args;
};

$hideThemesFromUpdateChecks = function($args, $url) use ($pusher)
{
    if (0 !== strpos($url, 'https://api.wordpress.org/themes/update-check')) {
        return $args;
    }

    $themes = json_decode($args['body']['themes'], true);

    $themesToHide = array_keys($pusher->themes->allPusherThemes());

    foreach ($themesToHide as $theme) {
        unset($themes['themes'][$theme]);

        if (isset($themes['active']) and in_array($themes['active'], $themesToHide)) {
            unset($themes['active']);
        }
    }

    $args['body']['themes'] = json_encode($themes);

    return $args;
};

add_filter('http_request_args', $hidePluginsFromUpdateChecks, 5, 2);
add_filter('http_request_args', $hideThemesFromUpdateChecks, 5, 2);

// Dismiss welcome hero
if (isset($_GET['wppusher-welcome']) and $_GET['wppusher-welcome'] == '0') {
    update_option('hide-wppusher-welcome', true);
}
