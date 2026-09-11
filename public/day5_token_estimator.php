<?php

declare(strict_types=1);

require __DIR__ . '/../src/AI/TokenEstimator.php';

use App\AI\TokenEstimator;

$estimator = new TokenEstimator();

$texts = [
    '你好',
    '你好，我叫小明。',
    '请介绍一下 PHP。',
    str_repeat('PHP 后端开发需要关注 API、数据库、缓存、队列。', 10),
];

foreach ($texts as $index => $text) {

    echo PHP_EOL;
    echo "==============================" . PHP_EOL;
    echo "文本 " . ($index + 1) . PHP_EOL;
    echo "==============================" . PHP_EOL;

    echo "字符数："
        . mb_strlen($text)
        . PHP_EOL;

    echo "估算 Token："
        . $estimator->estimateText($text)
        . PHP_EOL;
}