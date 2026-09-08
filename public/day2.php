<?php

declare(strict_types=1);
spl_autoload_register(function (string $class) {

    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../src/';

    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr(
        $class,
        strlen($prefix)
    );

    $file = $baseDir
        . str_replace('\\', '/', $relativeClass)
        . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
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
            'content' => '用简单的语言解释一下什么是依赖注入。'
        ]
    ]);

    echo "AI回答：" . PHP_EOL;
    echo $data['choices'][0]['message']['content'];
    echo PHP_EOL . PHP_EOL;

    echo "模型：" . $data['model'] . PHP_EOL;

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
    echo 123;die;
    echo $e->getMessage();
}

