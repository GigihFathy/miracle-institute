<?php

namespace App\Email\Events;

class VideoSessionScheduled
{
    public function __construct(
        public string $videoSessionId
    ) {}
}
