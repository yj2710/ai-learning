<?php

declare(strict_types=1);

require __DIR__ . '/../src/AI/AiClient.php';
require __DIR__ . '/../src/AI/ContextManagerV3.php';

use App\AI\AiClient;
use App\AI\ContextManagerV3;

$client = new AiClient();

$contextManager = new ContextManagerV3(3);

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

    // 1. 当前用户问题
    $currentUserMessage = [
        'role' => 'user',
        'content' => $question,
    ];

    // 2. 构造本次真正发送给 AI 的 Context
    $context = $contextManager->build(
        $messages,
        $currentUserMessage
    );

    // 3. 调用 AI
    $data = $client->chat($context);

    $answer = $data['choices'][0]['message']['content'];

    echo "用户：" . $question . PHP_EOL;
    echo "AI：" . $answer . PHP_EOL;

    // 4. 保存完整的一轮对话
    $messages[] = $currentUserMessage;

    $messages[] = [
        'role' => 'assistant',
        'content' => $answer,
    ];

    echo PHP_EOL;

    // 5. 查看本次真正发送给 AI 的 Context
    echo "当前 Context 消息数量："
        . count($context)
        . PHP_EOL;

    echo "Prompt Tokens："
        . $data['usage']['prompt_tokens']
        . PHP_EOL;

    echo "当前 Context：" . PHP_EOL;

    foreach ($context as $message) {
        echo $message['role']
            . '：'
            . $message['content']
            . PHP_EOL;
    }
}