<?php

declare(strict_types=1);

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

@ini_set('output_buffering', 'off');
@ini_set('zlib.output_compression', '0');

echo ": connected\n\n";

flush();

$apiKey = getenv('DEEPSEEK_API_KEY');

$baseUrl = rtrim(
    getenv('DEEPSEEK_BASE_URL')
        ?: 'https://api.deepseek.com',
    '/'
);

$model = getenv('DEEPSEEK_MODEL')
    ?: 'deepseek-v4-flash';

$url = $baseUrl . '/chat/completions';

$payload = [
    'model' => $model,
    'messages' => [
        [
            'role' => 'user',
            'content' => '请用 5 句话介绍 PHP。',
        ],
    ],
    'stream' => true,
];

$buffer = '';

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
        string $chunk
    ) use (&$buffer): int {

        $buffer .= $chunk;

        while (($pos = strpos($buffer, "\n\n")) !== false) {

            $event = substr(
                $buffer,
                0,
                $pos
            );

            $buffer = substr(
                $buffer,
                $pos + 2
            );

            $event = trim($event);

            if ($event === '') {
                continue;
            }

            if (!str_starts_with($event, 'data:')) {
                continue;
            }

            $json = trim(
                substr($event, 5)
            );

            if ($json === '[DONE]') {
                echo "event: done\n";
                echo "data: [DONE]\n\n";
                flush();
                continue;
            }

            $decoded = json_decode(
                $json,
                true
            );

            if (!is_array($decoded)) {
                continue;
            }

            $content =
                $decoded['choices'][0]['delta']['content']
                ?? null;

            if ($content !== null && $content !== '') {

                echo "data: "
                    . str_replace(
                        "\n",
                        "\\n",
                        $content
                    )
                    . "\n\n";

                flush();
            }
        }

        return strlen($chunk);
    },
]);

curl_exec($ch);

curl_close($ch);