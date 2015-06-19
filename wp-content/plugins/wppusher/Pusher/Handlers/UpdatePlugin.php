<?php

namespace Pusher\Handlers;

use Exception;
use Pusher\Commands\UpdatePlugin as UpdatePluginCommand;
use Pusher\Git\Repository;

class UpdatePlugin extends BaseHandler
{
    public function handle(UpdatePluginCommand $command)
    {
        $plugin = $this->pusher->plugins->pusherPluginFromRepository($command->repository);

        if (is_null($plugin)) throw new Exception('Could not find plugin.');

        $upgrader = $this->pusher->pluginUpgrader;

        $upgrader->upgradePlugin($plugin);

        do_action('wppusher_plugin_was_updated', $plugin->file);

        $this->pusher->log->info(
            "Plugin '{name}' was successfully updated. File: '{file}'",
            array('name' => $plugin->name, 'file' => $plugin->file)
        );

        $this->pusher->dashboard->addMessage('Plugin was successfully updated.');
    }
}
