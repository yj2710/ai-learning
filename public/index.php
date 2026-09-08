<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'ok' => true,
    'project' => 'ai-learning',
    'php_version' => PHP_VERSION,
    'message' => 'PHP 8.3 FPM is working.',
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
