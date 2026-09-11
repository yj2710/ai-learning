<?php

namespace App\AI;

class TokenEstimator
{
    public function estimateMessage(array $message): int
    {
        $content = (string) ($message['content'] ?? '');

        return $this->estimateText($content);
    }

    public function estimateText(string $text): int
    {
        if ($text === '') {
            return 0;
        }

        $length = mb_strlen($text);

        return (int) ceil($length / 2);
    }
}