<?php

declare(strict_types=1);

require __DIR__ . '/../src/AI/AiClient.php';

use App\AI\AiClient;

$client = new AiClient();

$messages = [
    [
        'role' => 'system',
        'content' => '你是一名 PHP 后端 AI 助手。回答问题时尽量简洁。',
    ],
    [
        'role' => 'user',
        'content' => '请用 3 句话介绍 PHP。',
    ],
];

try {

    $data = $client->chat($messages);

    $answer =
        $data['choices'][0]['message']['content']
        ?? '';

    echo "===== AI 回答 =====" . PHP_EOL;
    echo $answer . PHP_EOL;

    echo PHP_EOL;

    echo "===== Token =====" . PHP_EOL;

    echo "Prompt Tokens："
        . ($data['usage']['prompt_tokens'] ?? 0)
        . PHP_EOL;

    echo "Completion Tokens："
        . ($data['usage']['completion_tokens'] ?? 0)
        . PHP_EOL;

    echo "Total Tokens："
        . ($data['usage']['total_tokens'] ?? 0)
        . PHP_EOL;

} catch (\Throwable $e) {

    echo "发生错误：" . PHP_EOL;
    echo "类型：" . get_class($e) . PHP_EOL;
    echo "消息：" . $e->getMessage() . PHP_EOL;
}