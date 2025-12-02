<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStoredFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'in:pribadi,kantor,kuliah,umum'],
            'size_mb' => ['required', 'integer', 'min:0'],
            'storage_path' => ['required', 'string', 'max:255'],
            'mime_type' => ['required', 'string', 'max:120'],
            'is_public' => ['sometimes', 'boolean'],
            'expires_at' => ['nullable', 'date'],
        ];
    }
}
