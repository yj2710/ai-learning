<?php

declare(strict_types=1);
require_once __DIR__ . '/../src/AI/AiClient.php';
use App\AI\AiClient;

try {
    $aiClient = new AiClient();

    $data = $aiClient->chat([
        [
            'role' => 'system',
            'content' => '你是一名专业的 PHP 后端开发助手。'
        ],
        [
            'role' => 'user',
            'content' => <<<PROMPT
你是一名 PHP 后端工程师。

请分析 PHP 登录接口的核心步骤。

必须严格返回 JSON。

格式必须是：

{
  "steps": "这里必须是字符串",
  "summary": "这里必须是字符串"
}

不要输出 Markdown。
不要输出解释。
只输出 JSON。
PROMPT
//            'content' => <<<PROMPT
//你是一名资深 PHP 后端工程师。
//
//请分析一个 PHP 登录接口应该包含哪些核心步骤。
//
//要求：
//1. 使用 PHP 8.3
//2. 不使用 Laravel
//3. 不写代码
//4. 不写 SQL
//5. 不展开安全说明
//
//必须严格按照下面的 JSON 格式返回：
//
//{
//  "steps": [
//    "步骤1",
//    "步骤2",
//    "步骤3"
//  ],
//  "summary": "一句话总结"
//}
//
//除了 JSON 之外，不允许输出任何其他内容。
//PROMPT
        ]
    ]);
    $content = $data['choices'][0]['message']['content'];

    echo "AI原始回答：" . PHP_EOL;
    echo $content . PHP_EOL . PHP_EOL;

    $result = json_decode($content, true);

    if (!is_array($result)) {
        throw new RuntimeException('AI 返回的内容不是合法 JSON');
    }

    if (
        !isset($result['steps']) ||
        !is_array($result['steps'])
    ) {
        throw new RuntimeException('AI JSON 缺少 steps');
    }

    if (
        !isset($result['summary']) ||
        !is_string($result['summary'])
    ) {
        throw new RuntimeException('AI JSON 缺少 summary');
    }

    echo "PHP解析成功：" . PHP_EOL;

    echo "步骤：" . PHP_EOL;

    foreach ($result['steps'] as $index => $step) {
        echo ($index + 1) . '. ' . $step . PHP_EOL;
    }

    echo PHP_EOL;

    echo "总结：" . PHP_EOL;
    echo $result['summary'] . PHP_EOL;

    echo "结束原因：" .
        $data['choices'][0]['finish_reason'] .
        PHP_EOL;

    echo "Prompt Tokens：" .
        $data['usage']['prompt_tokens'] .
        PHP_EOL;

    echo "Completion Tokens：" .
        $data['usage']['completion_tokens'] .
        PHP_EOL;

    echo "Total Tokens：" .
        $data['usage']['total_tokens'] .
        PHP_EOL;
} catch (Exception $e) {
    echo "发生错误：" . PHP_EOL;
    echo "类型：" . get_class($e) . PHP_EOL;
    echo "消息：" . $e->getMessage() . PHP_EOL;
}

