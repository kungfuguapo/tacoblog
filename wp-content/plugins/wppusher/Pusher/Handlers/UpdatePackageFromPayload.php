<?php

namespace Pusher\Handlers;

use Pusher\Commands\UpdatePlugin;
use Pusher\Commands\UpdateTheme;
use Pusher\Commands\UpdatePackageFromPayload as UpdatePackageFromPayloadCommand;
use Pusher\Git\BitbucketWebhook;
use Pusher\Git\GitHubWebhook;
use Pusher\Git\GitLabWebhook;

class UpdatePackageFromPayload extends BaseHandler
{
    public function handle(UpdatePackageFromPayloadCommand $command)
    {
        // GitHub, Bitbucket or GitLab?
        if (isset($command->payload['repository']['html_url']) && strstr($command->payload['repository']['html_url'], 'github.com')) {
            $hook = new GitHubWebhook($command->payload);
        } else if (isset($command->payload['canon_url']) && strstr($command->payload['canon_url'], 'bitbucket.org')) {
            $hook = new BitbucketWebhook($command->payload);
        } else if (isset($command->payload['total_commits_count'])) {
            // It's probably GitLab then
            $hook = new GitLabWebhook($command->payload);
        } else {
            $this->pusher->log->error(
                "Push-to-Deploy failed. Payload couldn't be parsed."
            );

            return;
        }

        $repository = $hook->getRepository();

        // Plugin or theme command?
        if ( ! is_null($package = $this->pusher->plugins->pusherPluginFromRepository($repository))) {
            $command = new UpdatePlugin(array(
                'file' => $package->file,
                'repository' => (string) $repository
            ));
        } else if ( ! is_null($package = $this->pusher->themes->pusherThemeFromRepository($repository))) {
            $command = new UpdateTheme(array(
                'stylesheet' => $package->stylesheet,
                'repository' => (string) $repository
            ));
        } else {
            $this->pusher->log->error(
                "Push-to-Deploy failed. Couldn't find matching package."
            );

            return;
        }

        // Check if push to deploy is enabled before executing
        if ( ! $package->pushToDeploy) {
            $this->pusher->log->error(
                "Push-to-Deploy failed. Push-to-Deploy was not enabled for package '{name}'",
                array('name' => $package->name)
            );

            return;
        }

        $this->pusher->dashboard->execute($command);
    }
}
