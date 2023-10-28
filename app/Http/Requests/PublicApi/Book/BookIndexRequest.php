<?php

declare(strict_types=1);

namespace App\Http\Requests\PublicApi\Book;

use Illuminate\Foundation\Http\FormRequest;

class BookIndexRequest extends FormRequest
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
            'q' => 'string|min:3|max:512',
            'perPage' => 'int|numeric',
            'page' => 'int|numeric',
        ];
    }
}
