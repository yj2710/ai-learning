<?php

declare(strict_types=1);

require __DIR__ . '/../src/AI/AiClient.php';
require __DIR__ . '/../src/AI/TokenEstimator.php';
require __DIR__ . '/../src/AI/ContextManagerV4.php';

use App\AI\AiClient;
use App\AI\TokenEstimator;
use App\AI\ContextManagerV4;

$client = new AiClient();

$tokenEstimator = new TokenEstimator();

$contextManager = new ContextManagerV4(
    $tokenEstimator,
    2000
);

$messages = [
    [
        'role' => 'system',
        'content' => '你是一名 PHP 后端 AI 助手。回答问题时尽量简洁。',
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
    echo "Round " . ($index + 1) . PHP_EOL;
    echo "==============================" . PHP_EOL;

    $currentUserMessage = [
        'role' => 'user',
        'content' => $question,
    ];

    $context = $contextManager->build(
        $messages,
        $currentUserMessage
    );

    echo "Context 消息数量："
        . count($context)
        . PHP_EOL;

    echo "估算 Context Tokens：";

    $estimatedTokens = 0;

    foreach ($context as $message) {
        $estimatedTokens +=
            $tokenEstimator->estimateMessage($message);
    }

    echo $estimatedTokens . PHP_EOL;

    echo PHP_EOL;
    echo "当前 Context：" . PHP_EOL;

    foreach ($context as $message) {
        echo $message['role']
            . '：'
            . $message['content']
            . PHP_EOL;
    }

    try {

        $data = $client->chat($context);

        $answer =
            $data['choices'][0]['message']['content']
            ?? '';

        $messages[] = $currentUserMessage;

        $messages[] = [
            'role' => 'assistant',
            'content' => $answer,
        ];

        echo PHP_EOL;
        echo "API Prompt Tokens："
            . ($data['usage']['prompt_tokens'] ?? 0)
            . PHP_EOL;

        echo "API Completion Tokens："
            . ($data['usage']['completion_tokens'] ?? 0)
            . PHP_EOL;

        echo "API Total Tokens："
            . ($data['usage']['total_tokens'] ?? 0)
            . PHP_EOL;

    } catch (\Throwable $e) {

        echo PHP_EOL;
        echo "请求失败：" . PHP_EOL;
        echo "类型：" . get_class($e) . PHP_EOL;
        echo "消息：" . $e->getMessage() . PHP_EOL;
    }
}