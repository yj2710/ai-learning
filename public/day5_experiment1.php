<?php

declare(strict_types=1);

require __DIR__ . '/../src/AI/AiClient.php';

use App\AI\AiClient;

$client = new AiClient();

$questions = [
    '你好',
    '你好，我叫小明。',
    '请介绍一下 PHP。',
    '请用 5 句话介绍 PHP 8.3 的主要特点。',
    'Explain PHP backend development.',
];

foreach ($questions as $index => $question) {

    echo PHP_EOL;
    echo "==============================" . PHP_EOL;
    echo "实验 " . ($index + 1) . PHP_EOL;
    echo "==============================" . PHP_EOL;

    echo "问题：" . PHP_EOL;
    echo $question . PHP_EOL;

    $messages = [
        [
            'role' => 'user',
            'content' => $question,
        ]
    ];

    $data = $client->chat($messages);

    echo PHP_EOL;

    echo "Prompt Tokens："
        . ($data['usage']['prompt_tokens'] ?? 0)
        . PHP_EOL;

    echo "Completion Tokens："
        . ($data['usage']['completion_tokens'] ?? 0)
        . PHP_EOL;

    echo "Total Tokens："
        . ($data['usage']['total_tokens'] ?? 0)
        . PHP_EOL;
}