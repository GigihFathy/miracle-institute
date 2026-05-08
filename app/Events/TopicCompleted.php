<?php

namespace App\Events;

class TopicCompleted
{
    public function __construct(
        public string $topicProgressId
    ) {}
}