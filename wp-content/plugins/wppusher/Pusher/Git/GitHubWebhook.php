<?php

namespace Pusher\Git;

use Exception;

class GitHubWebhook implements Webhook
{
    protected $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    public function getRepository()
    {
        if ( ! isset($this->payload['repository']['full_name']))
            throw new Exception('Repository not found.');

        return new GitHubRepository($this->payload['repository']['full_name']);
    }
}
