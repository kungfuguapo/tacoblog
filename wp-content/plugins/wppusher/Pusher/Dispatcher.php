<?php

namespace Pusher;

class Dispatcher
{
    protected $pusher;

    public function __construct(Pusher $pusher)
    {
        $this->pusher = $pusher;
    }

    public function dispatchPostRequests()
    {
        if (isset($_POST['wppusher'])) {

            if ( ! current_user_can('update_plugins') || ! current_user_can('update_themes') ) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }

            $request = $_POST['wppusher'];

            switch ($request['action']) {
                case 'clear-log':
                    $this->pusher->dashboard->postClearLog($request);
                    break;

                case 'install-plugin':
                    $this->pusher->dashboard->postInstallPlugin($request);
                    break;

                case 'install-theme':
                    $this->pusher->dashboard->postInstallTheme($request);
                    break;

                case 'edit-plugin':
                    $this->pusher->dashboard->postEditPlugin($request);
                    break;

                case 'edit-theme':
                    $this->pusher->dashboard->postEditTheme($request);
                    break;

                case 'update-plugin':
                    $this->pusher->dashboard->postUpdatePlugin($request);
                    break;

                case 'update-theme':
                    $this->pusher->dashboard->postUpdateTheme($request);
                    break;

                case 'unlink-plugin':
                    $this->pusher->dashboard->postUnlinkPlugin($request);
                    break;

                case 'unlink-theme':
                    $this->pusher->dashboard->postUnlinkTheme($request);
                    break;

                default:
                    break;
            }
        }
    }

    public function dispatchWebhookRequest()
    {
        if ( ! isset($_GET['wppusher-hook']))
            return;

        if ( ! isset($_GET['token']) || $_GET['token'] !== get_option('wppusher_token')) {
            $this->pusher->log->error('Push-to-Deploy failed. Token was invalid.');
            status_header(400);
            die();
        }

        if (isset($_POST['payload']))
            $payload = json_decode(stripslashes($_POST['payload']), true);
        else
            $payload = json_decode(file_get_contents('php://input'), true);

        if ( ! $payload) {
            $this->pusher->log->error('Push-to-Deploy failed. No payload received.');
            status_header(400);
            die();
        }

        $this->pusher->log->info('Push-to-Deploy was initiated.');

        $this->pusher->dashboard->postWebhookPayload($payload);
    }
}
