<?php

namespace Pusher;

use Pusher\Git\RepositoryFactory;
use Pusher\License\LicenseManager;
use Pusher\License\WpShipperClient;
use Pusher\Log\Logger;
use Pusher\Services\TokenGenerator;
use Pusher\Pusher as Container;
use Pusher\Shell\PusherfileExecutor;
use Pusher\Storage\Database;
use Pusher\Storage\PluginRepository;
use Pusher\Storage\ThemeRepository;
use Pusher\WordPress\PluginUpgrader;
use Pusher\WordPress\PluginUpgraderSkin;
use Pusher\WordPress\ThemeUpgrader;
use Pusher\WordPress\ThemeUpgraderSkin;

class PusherServiceProvider implements ProviderInterface
{
    public function register(Container $pusher)
    {
        $pusher->db = function($pusher) { return new Database(); };
        $pusher->singleton('dashboard', function($pusher) { return new Dashboard($pusher); });
        $pusher->dispatcher = function($pusher) { return new Dispatcher($pusher); };
        $pusher->pluginUpgrader = function($pusher)
        {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
            include_once(ABSPATH . 'wp-admin/includes/file.php');
            include_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
            include_once(ABSPATH . 'wp-admin/includes/misc.php');

            return new PluginUpgrader($pusher, new PluginUpgraderSkin($pusher));
        };
        $pusher->themeUpgrader = function($pusher)
        {
            include_once(ABSPATH . 'wp-admin/includes/theme.php');
            include_once(ABSPATH . 'wp-admin/includes/file.php');
            include_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
            include_once(ABSPATH . 'wp-admin/includes/misc.php');

            return new ThemeUpgrader($pusher, new ThemeUpgraderSkin($pusher));
        };
        $pusher->plugins = function($pusher) { return new PluginRepository($pusher); };
        $pusher->themes = function($pusher) { return new ThemeRepository($pusher); };
        $pusher->repositoryFactory = function($pusher) { return new RepositoryFactory; };
        $pusher->pusherfileExecutor = function($pusher) { return new PusherfileExecutor($pusher); };
        $pusher->log = function($pusher) {
            return Logger::file(trailingslashit($pusher->pusherPath) . 'pusherlog');
        };
        $pusher->license = function($pusher)
        {
            $client = new WpShipperClient;
            return new LicenseManager($pusher, $client);
        };
        $pusher->tokens = function($pusher) { return new TokenGenerator; };
    }
}
