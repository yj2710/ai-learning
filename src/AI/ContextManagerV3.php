<?php

namespace App\AI;

class ContextManagerV3
{
    public function __construct(
        private int $maxTurns = 3
    ) {
    }

    public function build(
        array $history,
        array $currentUserMessage
    ): array {
        // 1. 找出 system message
        $systemMessages = [];

        // 2. 普通历史消息
        $conversationMessages = [];

        foreach ($history as $message) {
            if (($message['role'] ?? '') === 'system') {
                $systemMessages[] = $message;
                continue;
            }

            $conversationMessages[] = $message;
        }

        // 3. 把历史消息按照完整 Turn 分组
        $turns = [];
        $currentTurn = [];

        foreach ($conversationMessages as $message) {
            $currentTurn[] = $message;

            if (($message['role'] ?? '') === 'assistant') {
                $turns[] = $currentTurn;
                $currentTurn = [];
            }
        }

        // 4. 只保留最近 N 个完整 Turn
        $turns = array_slice(
            $turns,
            -$this->maxTurns
        );

        // 5. 重新展开
        $recentHistory = [];

        foreach ($turns as $turn) {
            foreach ($turn as $message) {
                $recentHistory[] = $message;
            }
        }

        // 6. system + 历史 + 当前用户问题
        return array_merge(
            $systemMessages,
            $recentHistory,
            [$currentUserMessage]
        );
    }
}