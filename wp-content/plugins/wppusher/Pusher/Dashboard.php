<?php

namespace Pusher;

use Exception;
use InvalidArgumentException;
use Pusher\Commands\EditPlugin;
use Pusher\Commands\EditTheme;
use Pusher\Commands\InstallPlugin;
use Pusher\Commands\InstallTheme;
use Pusher\Commands\UnlinkPlugin;
use Pusher\Commands\UnlinkTheme;
use Pusher\Commands\UpdatePlugin;
use Pusher\Commands\UpdateTheme;
use Pusher\Commands\UpdatePackageFromPayload;
use WP_Error;

class Dashboard
{
    public $messages = array();

    public function __construct(Pusher $pusher)
    {
        $this->pusher = $pusher;
    }

    public function getIndex()
    {
        $data['log'] = $this->pusher->log;
        $data['license_key'] = $this->pusher->license->licenseKey();

        return $this->render('index', $data);
    }

    public function postClearLog($request)
    {
        $this->pusher->log->clear();
        $this->addMessage('Log was cleared!');
    }

    public function getPlugins()
    {
        if (isset($_GET['repo'])) {
            $plugin = $this->pusher->plugins->pusherPluginFromRepository($_GET['repo']);

            if ($plugin) {
                return $this->render('plugins/edit', compact('plugin'));
            }
        }

        $data['plugins'] = $this->pusher->plugins->allPusherPlugins();

        return $this->render('plugins/index', $data);
    }

    public function postEditPlugin($request)
    {
        $command = new EditPlugin($request);
        $this->execute($command);
    }

    public function postUpdatePlugin($request)
    {
        $command = new UpdatePlugin($request);
        $this->execute($command);
    }

    public function getPluginsCreate()
    {
        // Run cleanup of orphan packages
        $this->pusher->db->cleanup();

        return $this->render('plugins/create');
    }

    public function postInstallPlugin($request)
    {
        $command = new InstallPlugin($request);
        $this->execute($command);
    }

    public function getThemes()
    {
        if (isset($_GET['repo'])) {
            $theme = $this->pusher->themes->pusherThemeFromRepository($_GET['repo']);

            if ($theme) {
                return $this->render('themes/edit', compact('theme'));
            }
        }

        $data['themes'] = $this->pusher->themes->allPusherThemes();

        return $this->render('themes/index', $data);
    }

    public function postEditTheme($request)
    {
        $command = new EditTheme($request);
        $this->execute($command);
    }

    public function postUpdateTheme($request)
    {
        $command = new UpdateTheme($request);
        $this->execute($command);
    }

    public function getThemesCreate()
    {
        // Run cleanup of orphan packages
        $this->pusher->db->cleanup();

        return $this->render('themes/create');
    }

    public function postInstallTheme($request)
    {
        $command = new InstallTheme($request);
        $this->execute($command);
    }

    public function postWebhookPayload($payload)
    {
        $command = new UpdatePackageFromPayload($payload);
        $this->execute($command);

        die();
    }

    public function postUnlinkPlugin($request)
    {
        $command = new UnlinkPlugin($request);
        $this->execute($command);
    }

    public function postUnlinkTheme($request)
    {
        $command = new UnlinkTheme($request);
        $this->execute($command);
    }

    public function addMessage($message)
    {
        $this->messages[] = $message;
    }

    public function execute($command)
    {
        $handlerClass = str_replace('Commands', 'Handlers', get_class($command));

        if ( ! class_exists($handlerClass)) {
            throw new InvalidArgumentException("Handler {$handlerClass} doesn't exist.");
        }

        $handler = new $handlerClass($this->pusher);

        try {
            $handler->handle($command);
        } catch (Exception $e) {
            $this->messages[] = new WP_Error('wppusher_error', $e->getMessage());
        }
    }

    protected function render($view, $data = array())
    {
        if ( ! current_user_can('update_plugins') || ! current_user_can('update_themes') ) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $data['messages'] = $this->messages;
        $data['hasValidLicense'] = $this->pusher->hasValidLicenseKey();
        $data['name'] = $this->pusher->getName();

        return include __DIR__.'/../views/base.php';
    }
}
