<?php

namespace Pusher\Handlers;

use Pusher\Commands\UnlinkPlugin as UnlinkPluginCommand;

class UnlinkPlugin extends BaseHandler
{
    public function handle(UnlinkPluginCommand $command)
    {
        $this->pusher->plugins->unlink($command->file);

        $this->pusher->dashboard->addMessage("Plugin was unlinked from WP Pusher. You can re-connect it with 'Dry run'.");
    }
}
