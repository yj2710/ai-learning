<?php

namespace App\AI;

class ContextManagerV2
{
    public function __construct(
        private int $maxTurns = 3
    ) {
    }

    public function trim(array $messages): array
    {
        // 1. 分离 system 和普通对话
        $systemMessages = [];
        $conversationMessages = [];

        foreach ($messages as $message) {
            if (($message['role'] ?? '') === 'system') {
                $systemMessages[] = $message;
                continue;
            }

            $conversationMessages[] = $message;
        }

        // 2. 把普通消息拆成完整对话轮次
        $turns = [];
        $currentTurn = [];

        foreach ($conversationMessages as $message) {
            $currentTurn[] = $message;

            if (($message['role'] ?? '') === 'assistant') {
                $turns[] = $currentTurn;
                $currentTurn = [];
            }
        }

        // 3. 只保留最近 N 轮
        $turns = array_slice(
            $turns,
            -$this->maxTurns
        );

        // 4. 重新展开消息
        $conversationMessages = [];

        foreach ($turns as $turn) {
            foreach ($turn as $message) {
                $conversationMessages[] = $message;
            }
        }

        // 5. system 永远放最前面
        return array_merge(
            $systemMessages,
            $conversationMessages
        );
    }
}