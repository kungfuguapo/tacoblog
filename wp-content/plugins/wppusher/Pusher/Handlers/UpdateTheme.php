<?php

namespace Pusher\Handlers;

use Pusher\Commands\UpdateTheme as UpdateThemeCommand;
use Pusher\Git\Repository;

class UpdateTheme extends BaseHandler
{
    public function handle(UpdateThemeCommand $command)
    {
        $theme = $this->pusher->themes->pusherThemeFromRepository($command->repository);

        $upgrader = $this->pusher->themeUpgrader;

        $upgrader->upgradeTheme($theme);

        do_action('wppusher_theme_was_updated', $theme->stylesheet);

        $this->pusher->log->info(
            "Theme '{name}' was successfully updated.",
            array('name' => $theme->name)
        );

        $this->pusher->dashboard->addMessage('Theme was successfully updated.');
    }
}
