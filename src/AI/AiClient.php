<?php

namespace App\AI;

class AiClient
{
    private string $apiKey;
    private string $baseUrl;
    private string $model;

    public function __construct()
    {
        $this->apiKey = getenv('DEEPSEEK_API_KEY');
        $this->baseUrl = getenv('DEEPSEEK_BASE_URL') ?: 'https://api.deepseek.com';
        $this->model = getenv('DEEPSEEK_MODEL') ?: 'deepseek-flash';
        if (!$this->apiKey) {
            throw new \RuntimeException("DEEPSEEK_API_KEY 未配置");
        }
    }

    /**
     * 发送聊天请求并返回响应数据
     * @param array $messages
     * @return array 返回API响应的数组数据
     */
    public function chat(array $messages): array
    {

        $payload = [
            'model' => $this->model,
            'messages' => $messages,
        ];
        $url = rtrim($this->baseUrl, '/') . '/chat/completions';
        // 初始化cURL会话，设置请求的基础URL
        $ch = curl_init($url);

        // 设置cURL选项数组
        curl_setopt_array($ch, [
            // 设置请求方法为POST
            CURLOPT_POST => true,

            // 设置HTTP请求头
            CURLOPT_HTTPHEADER => [
                // 设置认证Bearer令牌
                'Authorization: Bearer ' . $this->apiKey,
                // 设置内容类型为JSON
                'Content-Type: application/json',
            ],

            // 设置POST请求的JSON编码数据，保持Unicode和非转义斜杠
            CURLOPT_POSTFIELDS => json_encode(
                $payload,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ),

            // 将curl_exec()获取的信息以字符串返回而不是直接输出
            CURLOPT_RETURNTRANSFER => true,

            // 设置cURL执行的最大时间（秒）
            CURLOPT_TIMEOUT => 60,
        ]);

        // 执行cURL会话并获取响应
        $response = curl_exec($ch);

        // 检查cURL执行是否出错
        if ($response === false) {
            throw new \RuntimeException('cURL Error: ' . curl_error($ch));
        }

        // 获取HTTP状态码
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // 关闭cURL会话
        curl_close($ch);

        // 检查HTTP状态码是否表示错误（>=400）
        if ($httpCode >= 400) {
            throw new \RuntimeException("API Error: " . $response);
        }
        // 解析JSON响应数据
        $data = json_decode($response, true);
        // 检查解析后的数据是否为数组
        if (!is_array($data)) {
            throw new \RuntimeException("API 返回的 JSON 无法解析");
        }
        return $data;
    }
}