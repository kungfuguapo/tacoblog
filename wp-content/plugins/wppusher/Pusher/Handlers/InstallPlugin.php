<?php

namespace Pusher\Handlers;

use Exception;
use Pusher\Commands\InstallPlugin as InstallPluginCommand;
use Pusher\Git\BitbucketRepository;
use Pusher\Git\GitHubRepository;
use Pusher\Plugin;

class InstallPlugin extends BaseHandler
{
    public function handle(InstallPluginCommand $command)
    {
        $plugin = new Plugin;

        $repository = $this->pusher->repositoryFactory->build(
            $command->type,
            $command->repository
        );

        if ($command->private and $this->pusher->hasValidLicenseKey()) {
            $repository->makePrivate();
        }

        $repository->setBranch($command->branch);
        $plugin->setRepository($repository);
        $plugin->setSubdirectory($command->subdirectory);

        $upgrader = $this->pusher->pluginUpgrader;

        $result = ($command->dryRun)
            ? true
            : $upgrader->installPlugin($plugin);

        if ($result !== true) return;

        if ($command->subdirectory) {
            $slug = end(explode('/', $command->subdirectory));
        } else {
            $slug = $repository->getSlug();
        }

        $plugin = $this->pusher->plugins->fromSlug($slug);
        $plugin->setRepository($repository);
        $plugin->setPushToDeploy($command->ptd);
        $plugin->setSubdirectory($command->subdirectory);

        $this->pusher->plugins->store($plugin);

        $baseAdminUrl = (is_multisite()) ? network_admin_url() : get_admin_url();
        $activationLink = $baseAdminUrl
            . "plugins.php?action=activate&plugin="
            . urlencode($plugin->file)
            . "&_wpnonce="
            . wp_create_nonce('activate-plugin_' . $plugin->file);

        do_action('wppusher_plugin_was_installed', $plugin->file);

        $this->pusher->log->info(
            "Plugin '{name}' was successfully installed. File: '{file}'",
            array('name' => $plugin->name, 'file' => $plugin->file)
        );

        $this->pusher->dashboard->addMessage("Plugin was successfully installed. Go ahead and <a href=\"{$activationLink}\">activate</a> it.");
    }
}
