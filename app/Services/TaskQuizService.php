<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class TaskQuizService
{
    protected function apiKey(): string
    {
        $key = config('services.openai.api_key');

        if (! $key) {
            throw new RuntimeException('The verification task is not configured yet. Please contact support.');
        }

        return $key;
    }

    protected function model(): string
    {
        return config('services.openai.model', 'gpt-4o-mini');
    }

    protected function cacheKey(int $userId, int $orderId): string
    {
        return "task-quiz:{$userId}:{$orderId}";
    }

    /**
     * Ask OpenAI for one short quiz question + its expected answer. The
     * answer is cached server-side only and never sent to the client.
     */
    public function generateQuestion(int $userId, int $orderId): string
    {
        $response = Http::withToken($this->apiKey())
            ->timeout(20)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model(),
                'messages' => [
                    ['role' => 'system', 'content' =>
                        'You generate one short general-knowledge trivia question for a quick claim-verification quiz '.
                        'in a rewards app. Respond ONLY with strict JSON in this exact shape: '.
                        '{"question": "...", "answer": "..."}. The question must have a single short, unambiguous '.
                        'factual answer (a word, number, or short phrase) and must be answerable in a few seconds.'],
                ],
                'temperature' => 0.9,
                'response_format' => ['type' => 'json_object'],
            ]);

        if ($response->failed()) {
            Log::error('OpenAI question generation failed', ['body' => $response->body()]);
            throw new RuntimeException('Could not load your task right now. Please try again.');
        }

        $data = json_decode((string) $response->json('choices.0.message.content'), true);

        $question = $data['question'] ?? null;
        $answer   = $data['answer'] ?? null;

        if (! $question || ! $answer) {
            throw new RuntimeException('Could not load your task right now. Please try again.');
        }

        Cache::put($this->cacheKey($userId, $orderId), [
            'question' => $question,
            'answer'   => $answer,
        ], now()->addMinutes(10));

        return $question;
    }

    /**
     * Verify the user's answer against the cached question via an OpenAI
     * judge call. The cached question is consumed (one attempt per question)
     * regardless of the outcome.
     */
    public function checkAnswer(int $userId, int $orderId, string $userAnswer): bool
    {
        $key    = $this->cacheKey($userId, $orderId);
        $cached = Cache::get($key);

        if (! $cached) {
            throw new RuntimeException('Your task expired. Please try again.');
        }

        Cache::forget($key);

        $response = Http::withToken($this->apiKey())
            ->timeout(20)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model(),
                'messages' => [
                    ['role' => 'system', 'content' =>
                        'You grade a short trivia answer for a rewards app. Given the question, the expected '.
                        'answer, and the user\'s answer, decide if the user\'s answer is correct. Allow for minor '.
                        'spelling, phrasing, capitalization, or synonym differences, but reject answers that are '.
                        'actually wrong. Respond ONLY with strict JSON in this exact shape: {"correct": true} or '.
                        '{"correct": false}.'],
                    ['role' => 'user', 'content' => json_encode([
                        'question'        => $cached['question'],
                        'expected_answer' => $cached['answer'],
                        'user_answer'     => $userAnswer,
                    ])],
                ],
                'temperature' => 0,
                'response_format' => ['type' => 'json_object'],
            ]);

        if ($response->failed()) {
            Log::error('OpenAI answer check failed', ['body' => $response->body()]);
            throw new RuntimeException('Could not verify your response right now. Please try again.');
        }

        $data = json_decode((string) $response->json('choices.0.message.content'), true);

        if (! is_array($data) || ! array_key_exists('correct', $data)) {
            throw new RuntimeException('Could not verify your response right now. Please try again.');
        }

        return (bool) $data['correct'];
    }
}
