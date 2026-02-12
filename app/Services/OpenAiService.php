<?php

namespace App\Services;
use Illuminate\Http\UploadedFile;
use OpenAI\Factory;

class OpenAiService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }

    public function generatePromtForImage(UploadedFile $image): string
    {
      try {
        $imageData = base64_encode(file_get_contents($image->getPathname()));
        $mimeType = $image->getMimeType();

        $client = (new Factory())->withApiKey(config('services.openai.key'))->make();
        $response = $client->chat()->create([
        'model' => 'gpt-5.2',
            'messages' => [
                [
                    "role" => 'user',
                    "content" => [
                        [
                            "type" => 'text',
                            "text" => 'Analyze this image and generate a detailed, descriptive promt that could be used to recreate
                             a similar image with AI image generation tools. The prompt should be comprehesive,
                             decribing the visual elements, style, compsition, lighting,
                             colors, and any other relevant details, Make it detailed enough that someone could use it to generate a similar image.'
                        ],
                        [
                            "type" => 'image_url',
                            "image_url" => [
                                "url" => 'data:' . $mimeType. ';base64,' . $imageData,
                            ]
                        ]
                    ],
                ]
            ],
        ]);

        // Logic to call OpenAI API and generate an image based on the prompt
      
        return $response->choices[0]->message->content;

        } catch (RateLimitException $e) {
            return 'OpenAI rate limit exceeded. Please try again later.';
        } catch (\Exception $e) {
            return 'OpenAI error: ' . $e->getMessage();
        }
    }
}
