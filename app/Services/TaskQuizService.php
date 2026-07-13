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
     * A broad spread of everyday/world topics. Picked server-side (rather than
     * left to the model's own judgement) so questions actually rotate across
     * categories instead of the model defaulting to "capital of X" every time.
     */
    protected function categories(): array
    {
        return [
            'science and nature',
            'world history',
            'geography and landmarks (not capital cities)',
            'technology and inventions',
            'arts, literature, and music',
            'sports and games',
            'food and cuisine from around the world',
            'space and astronomy',
            'animals and wildlife',
            'the human body and health',
            'everyday general knowledge',
            'famous people and their achievements',
        ];
    }

    /**
     * Ask OpenAI for one short quiz question + its expected answer. The
     * answer is cached server-side only and never sent to the client.
     */
    public function generateQuestion(int $userId, int $orderId): string
    {
        $categories = $this->categories();
        $category   = $categories[array_rand($categories)];

        $response = Http::withToken($this->apiKey())
            ->timeout(20)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model(),
                'messages' => [
                    ['role' => 'system', 'content' =>
                        'You generate one short trivia question for a quick claim-verification quiz in a rewards '.
                        'app, drawing on different aspects of everyday life and the world. Respond ONLY with '.
                        'strict JSON in this exact shape: {"question": "...", "answer": "..."}. The question must '.
                        'have a single short, unambiguous factual answer (a word, number, or short phrase) and must '.
                        'be answerable in a few seconds. Avoid repetitive, overused trivia patterns such as '.
                        '"what is the capital of X" — keep the phrasing and angle fresh each time.'],
                    ['role' => 'user', 'content' => "Generate a trivia question specifically about this topic: {$category}."],
                ],
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
