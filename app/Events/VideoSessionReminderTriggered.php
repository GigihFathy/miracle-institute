<?php

namespace App\Events;

class VideoSessionReminderTriggered
{
    public function __construct(
        public string $videoSessionId
    ) {}
}