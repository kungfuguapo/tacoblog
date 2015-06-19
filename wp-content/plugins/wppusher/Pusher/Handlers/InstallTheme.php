<?php

namespace Pusher\Handlers;

use Pusher\Commands\InstallTheme as InstallThemeCommand;
use Pusher\Git\BitbucketRepository;
use Pusher\Git\GitHubRepository;
use Pusher\Theme;

class InstallTheme extends BaseHandler
{
    public function handle(InstallThemeCommand $command)
    {
        $theme = new Theme;

        $repository = $this->pusher->repositoryFactory->build(
            $command->type,
            $command->repository
        );

        if ($command->private and $this->pusher->hasValidLicenseKey()) {
            $repository->makePrivate();
        }

        $repository->setBranch($command->branch);

        $theme->setRepository($repository);
        $theme->setSubdirectory($command->subdirectory);

        $upgrader = $this->pusher->themeUpgrader;

        $result = ($command->dryRun)
            ? true
            : $upgrader->installTheme($theme);

        if ($result !== true) return;

        if ($command->subdirectory) {
            $slug = end(explode('/', $command->subdirectory));
        } else {
            $slug = $repository->getSlug();
        }

        $theme = $this->pusher->themes->fromSlug($slug);
        $theme->setRepository($repository);
        $theme->setPushToDeploy($command->ptd);
        $theme->setSubdirectory($command->subdirectory);

        $this->pusher->themes->store($theme);

        if (is_multisite()) {
            $activationLink = network_admin_url()
                . "themes.php?action=enable&theme="
                . urlencode($theme->stylesheet)
                . "&_wpnonce="
                . wp_create_nonce('enable-theme_' . $theme->stylesheet);
        } else {
            $activationLink = get_admin_url()
                . "themes.php?action=activate&stylesheet="
                . urlencode($theme->stylesheet)
                . "&_wpnonce="
                . wp_create_nonce('switch-theme_' . $theme->stylesheet);
        }

        do_action('wppusher_theme_was_installed', $theme->stylesheet);

        $this->pusher->log->info(
            "Theme '{name}' was successfully installed.",
            array('name' => $theme->name)
        );

        $this->pusher->dashboard->addMessage("Theme was successfully installed. Go ahead and <a href=\"{$activationLink}\">activate</a> it.");
    }
}
