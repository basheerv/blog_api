<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\ImageGenerationRequest;
use App\Services\OpenAiService;
use Illuminate\Support\Str;
class ImageGenerationController extends Controller
{

    public function __construct(OpenAiService $openAiService)
    {
        $this->openAiService = $openAiService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $imageGenerations = $request->user()->imageGenerations()->latest()->get();
        return response()->json($imageGenerations);
    }

    /**
     * Handle the incoming request to generate an image prompt.
     */

    public function store(ImageGenerationRequest $request)
    {
        $user = $request->user();
        $image= $request->file('image');

        $originalName = $image->getClientOriginalName();
        $sanitizedOriginalName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
        $extension = $image->getClientOriginalExtension();
        $originalName = $sanitizedOriginalName . '_' . Str::random(10) . '.' . $extension;



        $path = $image->storeAs('images', $originalName, 'public');

        $imagePrompt = $this->openAiService->generatePromtForImage($image);

        $imageGeneration = $user->imageGenerations()->create([
            'image_path' => $path,
            'generated_prompt' => $imagePrompt,
            'image_name' => $originalName,
            'mime_type' => $image->getMimeType(),
            'image_size' => $image->getSize(),
        ]);

        return response()->json($imageGeneration, 201);
    }
}
