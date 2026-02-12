<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImageGenerationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'image' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:10240', // Max size in 10MB
                'dimensions:min_width=100,min_height=100,max_width=5000,max_height=5000',
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'An image file is required.',
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
            'image.max' => 'The image may not be greater than 10MB.',
            'image.dimensions' => 'The image dimensions must be between 100x100 and 5000x5000 pixels.',
        ];
    }
}
