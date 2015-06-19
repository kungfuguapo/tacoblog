<?php

namespace Pusher\Git;

use Exception;

class BitbucketWebhook implements Webhook
{
    protected $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    public function getRepository()
    {
        if ( ! isset($this->payload['repository']['owner']) || ! isset($this->payload['repository']['slug']))
            throw new Exception('Repository not found.');

        $handle = $this->payload['repository']['owner'] . '/' . $this->payload['repository']['slug'];

        return new BitbucketRepository($handle);
    }
}
