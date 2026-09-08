<?php

declare(strict_types=1);

$apiKey = getenv('DEEPSEEK_API_KEY');
$baseUrl = getenv('DEEPSEEK_BASE_URL') ?: 'https://api.deepseek.com';
$model = getenv('DEEPSEEK_MODEL') ?: 'deepseek-v4-flash';

if (!$apiKey) {
    die("DEEPSEEK_API_KEY 未配置\n");
}

$url = rtrim($baseUrl, '/') . '/chat/completions';

$payload = [
    'model' => $model,
    'messages' => [
        [
            'role' => 'system',
            'content' => '你是一个专业的 PHP 后端开发助手。',
        ],
        [
            'role' => 'user',
            'content' => '请用一句话解释什么是 PHP。',
        ],
    ],
];

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_POST => true,

    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
    ],

    CURLOPT_POSTFIELDS => json_encode(
        $payload,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    ),

    CURLOPT_RETURNTRANSFER => true,

    CURLOPT_TIMEOUT => 60,
]);

$response = curl_exec($ch);

if ($response === false) {
    die('cURL Error: ' . curl_error($ch) . PHP_EOL);
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

echo "HTTP Status: {$httpCode}\n\n";

if ($httpCode >= 400) {
    echo "API Error:\n";
    echo $response . PHP_EOL;
    exit(1);
}

$data = json_decode($response, true);

if (!is_array($data)) {
    die("API 返回的 JSON 无法解析\n");
}

//echo "AI:\n";
//echo $data['choices'][0]['message']['content'] ?? '没有返回内容';
//echo PHP_EOL;

echo json_encode(
    $data,
    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
);

echo PHP_EOL;