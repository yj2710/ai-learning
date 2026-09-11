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
        'content' => '你是一名 PHP 后端 AI 助手。回答问题时尽量简洁。',
    ],
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

    $currentUserMessage = [
        'role' => 'user',
        'content' => $question,
    ];

    /*
     * 构建本次真正发送给模型的 Context
     */
    $context = $contextManager->build(
        $messages,
        $currentUserMessage
    );

    echo "当前 Context：" . PHP_EOL;

    foreach ($context as $message) {
        echo $message['role']
            . '：'
            . $message['content']
            . PHP_EOL;
    }

    echo PHP_EOL;

    /*
     * 请求 AI
     */
    $data = $client->chat($context);

    $answer =
        $data['choices'][0]['message']['content']
        ?? '';

    /*
     * 把本轮完整对话加入 History
     */
    $messages[] = $currentUserMessage;

    $messages[] = [
        'role' => 'assistant',
        'content' => $answer,
    ];

    echo "AI：" . PHP_EOL;
    echo $answer . PHP_EOL;

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