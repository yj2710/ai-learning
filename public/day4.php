<?php

require_once __DIR__ . '/../src/AI/AiClient.php';

use App\AI\AiClient;

$client = new AiClient();
$messages = [];

$questions = [
    '我叫小明。',
    '我是一名 PHP 后端开发。',
    '我正在学习 AI。',
    '我的名字是什么？',
    '我的职业是什么？',
    '我正在学习什么？',
];

foreach ($questions as $index => $question) {

    echo PHP_EOL;
    echo "==============================" . PHP_EOL;
    echo "第 " . ($index + 1) . " 轮对话" . PHP_EOL;
    echo "==============================" . PHP_EOL;

    // 1. 把用户消息加入 Context
    $messages[] = [
        'role' => 'user',
        'content' => $question,
    ];

    // 2. 打印当前 Context
    echo PHP_EOL;
    echo "当前 Context：" . PHP_EOL;

    foreach ($messages as $message) {
        echo $message['role']
            . '：'
            . $message['content']
            . PHP_EOL;
    }

    echo PHP_EOL;

    // 3. 调用 AI
    $data = $client->chat($messages);

    $answer = $data['choices'][0]['message']['content'];

    // 4. 输出 AI 回答
    echo "AI：" . $answer . PHP_EOL;

    // 5. 把 AI 回答加入 Context
    $messages[] = [
        'role' => 'assistant',
        'content' => $answer,
    ];

    // 6. 输出 Token
    echo PHP_EOL;
    echo "Prompt Tokens："
        . $data['usage']['prompt_tokens']
        . PHP_EOL;

    echo "Completion Tokens："
        . $data['usage']['completion_tokens']
        . PHP_EOL;

    echo "Total Tokens："
        . $data['usage']['total_tokens']
        . PHP_EOL;
}