<?php

namespace Pusher\Commands;

class UpdatePackageFromPayload
{
    public $payload;

    public function __construct($payload)
    {
        $this->payload = $payload;
    }
}
