<?php

namespace Efihub\Modules;

use Efihub\EfihubClient;

/**
 * AI Service client for EFIHUB.
 *
 * Base path: /storage
 */
class AIClient
{
    /** @var EfihubClient */
    private $client;

    public function __construct(EfihubClient $client)
    {
        $this->client = $client;
    }

    /**
     * Generate text using the specified model, prompt, and instructions.
     * 
     * @param string $model The model to use for text generation.
     * @param string $prompt The prompt to provide to the model.
     * @param string|null $instructions Optional instructions to guide the model's response.
     * @param array|null $attachments Optional attachments to include in the request.
     * 
     * @return string|null The generated text on success, or null on failure.
     */
    public function text(
        string $model,
        string $prompt,
        ?string $instructions = null,
        ?array $attachments = []
    ): ?string {
        if ($instructions) {
            $instructions = <<<PROMPT
            $instructions
            PROMPT;
        }

        // Call the API endpoint to generate text using the specified model, prompt, and instructions.
        $res = $this->client->postMultipart('/ai/generate/text', [
            'model' => $model,
            'prompt' => $prompt,
            'instructions' => $instructions,
        ], $attachments);
        if (!$res->successful()) {
            return null;
        }

        // Get the generated text from the response
        return $res->json('data.content') ?? null;
    }

    /**
     * Generate an image using the specified model, prompt, instructions, and options.
     * 
     * @param string $model The model to use for image generation.
     * @param string $prompt The prompt to provide to the model.
     * @param string|null $instructions Optional instructions to guide the model's response.
     * @param array|null $attachments Optional attachments to include in the request.
     * @param array|null $options Optional parameters such as 'ratio' and 'quality'.
     * 
     * @return string|array|null The URL of the generated image on success, array of URLs, or null on failure.
     */
    public function image(
        string $model,
        string $prompt,
        ?string $instructions = null,
        ?array $attachments = [],
        ?array $options = []
    ): string|array|null {

        // If instructions are provided, format them as a prompt for the model.
        if ($instructions) {
            $instructions = <<<PROMPT
            $instructions
            PROMPT;
        }

        // Call the API endpoint to generate an image using the specified model, prompt, and instructions.
        $res = $this->client->postMultipart('/ai/generate/image', [
            'model' => $model,
            'prompt' => $prompt,
            'instructions' => $instructions,
            'ratio' => $options['ratio'] ?? 'landscape',
            'quality' => $options['quality'] ?? 'medium',
        ], $attachments);
        if (!$res->successful()) {
            return null;
        }

        // Get the generated image URL / URLs from the response. If multiple images are generated, return an array of URLs.
        return $res->json('data.content') ?? null;
    }
}
