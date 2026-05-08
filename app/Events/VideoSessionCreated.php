<?php

namespace App\Events;

class VideoSessionCreated
{
    public function __construct(
        public string $videoSessionId
    ) {}
}