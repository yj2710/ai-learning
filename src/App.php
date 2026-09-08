<?php

declare(strict_types=1);

namespace App;

final class App
{
    public function health(): array
    {
        return [
            'ok' => true,
            'message' => 'PHP 8.3 AI learning environment is running.',
            'php_version' => PHP_VERSION,
        ];
    }
}
