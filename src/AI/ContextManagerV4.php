<?php

namespace App\AI;

class ContextManagerV4
{
    public function __construct(
        private TokenEstimator $tokenEstimator,
        private int            $maxTokens = 2000
    )
    {
    }

    /**
     * 根据 Token Budget 构建本次请求 Context。
     *
     * 规则：
     * 1. System 永远保留
     * 2. 历史按完整 Turn 保存
     * 3. 从最近的 Turn 开始向前加入
     * 4. 当前用户消息永远保留
     * 5. 超过 Token Budget 后，不再加入更旧的 Turn
     */
    public function build(
        array $history,
        array $currentUserMessage
    ): array
    {
        $systemMessages = [];
        $conversationMessages = [];

        foreach ($history as $message) {
            if (($message['role'] ?? '') === 'system') {
                $systemMessages[] = $message;
                continue;
            }

            $conversationMessages[] = $message;
        }

        // 把历史消息组织成完整 Turn
        $turns = [];
        $currentTurn = [];

        foreach ($conversationMessages as $message) {
            $currentTurn[] = $message;

            if (($message['role'] ?? '') === 'assistant') {
                $turns[] = $currentTurn;
                $currentTurn = [];
            }
        }

        // 如果最后存在不完整 Turn，这里暂时忽略
        // 因为我们要求历史 Turn 必须是完整的 user + assistant
        $selectedTurns = [];

        // 当前用户消息必须保留
        $currentUserTokens = $this->tokenEstimator->estimateMessage($currentUserMessage);

        // System 先计算
        $usedTokens = 0;

        foreach ($systemMessages as $message) {
            $usedTokens += $this->tokenEstimator->estimateMessage($message);
        }

        $usedTokens += $currentUserTokens;

        // 从最近的 Turn 开始向前加入
        for ($i = count($turns) - 1; $i >= 0; $i--) {

            $turn = $turns[$i];

            $turnTokens = 0;

            foreach ($turn as $message) {
                $turnTokens +=
                    $this->tokenEstimator->estimateMessage($message);
            }

            if ($usedTokens + $turnTokens > $this->maxTokens) {
                break;
            }

            array_unshift($selectedTurns, $turn);

            $usedTokens += $turnTokens;
        }

        $recentHistory = [];

        foreach ($selectedTurns as $turn) {
            foreach ($turn as $message) {
                $recentHistory[] = $message;
            }
        }

        return array_merge(
            $systemMessages,
            $recentHistory,
            [$currentUserMessage]
        );
    }
}