<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoredFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:150'],
            'description' => ['sometimes', 'nullable', 'string'],
            'category' => ['sometimes', 'in:pribadi,kantor,kuliah,umum'],
            'size_mb' => ['sometimes', 'integer', 'min:0'],
            'storage_path' => ['sometimes', 'string', 'max:255'],
            'mime_type' => ['sometimes', 'string', 'max:120'],
            'is_public' => ['sometimes', 'boolean'],
            'expires_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
