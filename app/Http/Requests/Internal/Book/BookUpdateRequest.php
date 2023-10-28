<?php

declare(strict_types=1);

namespace App\Http\Requests\Internal\Book;

use Illuminate\Foundation\Http\FormRequest;

class BookUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('publisher');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'string',
            'description' => 'string',
            'author' => 'string|max:255',
            'cover' => 'file|image|mimetypes:image/jpeg,image/png',
            'price' => 'decimal:2',
        ];
    }
}
