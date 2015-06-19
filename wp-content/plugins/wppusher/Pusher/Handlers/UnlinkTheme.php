<?php

namespace Pusher\Handlers;

use Pusher\Commands\UnlinkTheme as UnlinkThemeCommand;

class UnlinkTheme extends BaseHandler
{
    public function handle(UnlinkThemeCommand $command)
    {
        $this->pusher->themes->unlink($command->stylesheet);

        $this->pusher->dashboard->addMessage("Theme was unlinked from WP Pusher. You can re-connect it with 'Dry run'.");
    }
}
