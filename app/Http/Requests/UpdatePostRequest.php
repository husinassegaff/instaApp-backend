<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
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
            'caption' => 'nullable|string|max:2200',
            'image' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    // Only validate if image is provided
                    if (empty($value)) {
                        return;
                    }

                    // Validate base64 image format
                    if (!preg_match('/^data:image\/(\w+);base64,/', $value, $matches)) {
                        $fail('The image must be a valid base64 encoded image.');
                        return;
                    }

                    // Validate image type
                    $allowedTypes = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
                    if (!in_array(strtolower($matches[1]), $allowedTypes)) {
                        $fail('The image type must be one of: jpeg, jpg, png, gif, webp.');
                    }
                },
            ],
        ];
    }
}
