<?php

declare(strict_types=1);

require __DIR__ . '/../src/AI/AiClient.php';

use App\AI\AiClient;

$client = new AiClient();

$shortMessages = [
    [
        'role' => 'user',
        'content' => '你好',
    ],
    [
        'role' => 'assistant',
        'content' => '你好！',
    ],
    [
        'role' => 'user',
        'content' => '你是谁？',
    ],
    [
        'role' => 'assistant',
        'content' => '我是 AI 助手。',
    ],
    [
        'role' => 'user',
        'content' => '谢谢',
    ],
];

$longText = str_repeat(
    'PHP 后端开发需要关注 API、数据库、缓存、队列、日志、异常处理和系统性能。 ',
    100
);

$longMessages = [
    [
        'role' => 'user',
        'content' => $longText,
    ],
    [
        'role' => 'assistant',
        'content' => $longText,
    ],
    [
        'role' => 'user',
        'content' => $longText,
    ],
    [
        'role' => 'assistant',
        'content' => $longText,
    ],
    [
        'role' => 'user',
        'content' => $longText,
    ],
];

$tests = [
    '短消息 Context' => $shortMessages,
    '长消息 Context' => $longMessages,
];

foreach ($tests as $name => $messages) {

    echo PHP_EOL;
    echo "==============================" . PHP_EOL;
    echo $name . PHP_EOL;
    echo "==============================" . PHP_EOL;

    echo "消息数量：" . count($messages) . PHP_EOL;

    try {

        $data = $client->chat($messages);

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

        echo "请求失败：" . PHP_EOL;
        echo "类型：" . get_class($e) . PHP_EOL;
        echo "消息：" . $e->getMessage() . PHP_EOL;
    }
}