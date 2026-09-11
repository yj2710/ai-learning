<?php

declare(strict_types=1);

require __DIR__ . '/../src/AI/AiClient.php';

use App\AI\AiClient;

$client = new AiClient();

$messages = [
    [
        'role' => 'user',
        'content' => '请用 5 句话介绍 PHP。',
    ],
];

$payload = [
    'model' => getenv('DEEPSEEK_MODEL') ?: 'deepseek-v4-flash',
    'messages' => $messages,
    'stream' => true,
];

$url = rtrim(
        getenv('DEEPSEEK_BASE_URL')
            ?: 'https://api.deepseek.com',
        '/'
    ) . '/chat/completions';

$apiKey = getenv('DEEPSEEK_API_KEY');

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_POST => true,

    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
    ],

    CURLOPT_POSTFIELDS => json_encode(
        $payload,
        JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
    ),

    CURLOPT_RETURNTRANSFER => false,

    CURLOPT_TIMEOUT => 60,

    CURLOPT_WRITEFUNCTION => function (
        $ch,
        string $data
    ): int {

        echo "===== 收到 Chunk =====" . PHP_EOL;

        echo $data;

        echo PHP_EOL;

        return strlen($data);
    },
]);

$result = curl_exec($ch);

if ($result === false) {
    echo PHP_EOL;
    echo "cURL Error：" . curl_error($ch) . PHP_EOL;
}

curl_close($ch);