<?php

declare(strict_types=1);

require __DIR__ . '/../src/AI/AiClient.php';
require __DIR__ . '/../src/AI/ContextManagerV2.php';

use App\AI\AiClient;
use App\AI\ContextManagerV2;

$client = new AiClient();

$contextManager = new ContextManagerV2(3);

$messages = [
    [
        'role' => 'system',
        'content' => '你是一名 PHP 后端 AI 助手。回答问题时尽量简洁。'
    ]
];

$questions = [
    '我叫小明。',
    '我的职业是 PHP 后端开发。',
    '我正在学习 AI。',
    '我喜欢研究 AI Agent。',
    '我正在学习 RAG。',
    '我正在学习 MCP。',
    '我正在学习 Python。',
    '我最终想成为 AI Agent 工程师。',
];

foreach ($questions as $index => $question) {

    echo PHP_EOL;
    echo "==============================" . PHP_EOL;
    echo "第 " . ($index + 1) . " 轮" . PHP_EOL;
    echo "==============================" . PHP_EOL;

    // 1. 加入当前用户问题
    $messages[] = [
        'role' => 'user',
        'content' => $question,
    ];

    // 2. 当前 user 不能在这里 trim
    $data = $client->chat($messages);

    $answer = $data['choices'][0]['message']['content'];

    echo "用户：" . $question . PHP_EOL;
    echo "AI：" . $answer . PHP_EOL;

    // 3. 加入 AI 回答
    $messages[] = [
        'role' => 'assistant',
        'content' => $answer,
    ];

    // 4. 一轮完整后，再裁剪 Context
    $messages = $contextManager->trim($messages);

    echo PHP_EOL;

    echo "当前 Context 消息数量："
        . count($messages)
        . PHP_EOL;

    echo "Prompt Tokens："
        . $data['usage']['prompt_tokens']
        . PHP_EOL;

    echo "当前 Context：" . PHP_EOL;

    foreach ($messages as $message) {
        echo $message['role']
            . '：'
            . $message['content']
            . PHP_EOL;
    }
}