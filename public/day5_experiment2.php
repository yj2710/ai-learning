<?php

declare(strict_types=1);

require __DIR__ . '/../src/AI/AiClient.php';

use App\AI\AiClient;

$client = new AiClient();

$messages = [];

$questions = [
    '我叫小明。',
    '我的职业是 PHP 后端开发。',
    '我正在学习 AI。',
    '我喜欢研究 AI Agent。',
    '我正在学习 RAG。',
];

foreach ($questions as $index => $question) {

    echo PHP_EOL;
    echo "==============================" . PHP_EOL;
    echo "Round " . ($index + 1) . PHP_EOL;
    echo "==============================" . PHP_EOL;

    $messages[] = [
        'role' => 'user',
        'content' => $question,
    ];

    $data = $client->chat($messages);

    $answer = $data['choices'][0]['message']['content'] ?? '';

    $messages[] = [
        'role' => 'assistant',
        'content' => $answer,
    ];

    echo "当前消息数量："
        . count($messages)
        . PHP_EOL;

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