<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Import;

use Illuminate\Foundation\Http\FormRequest;

class ImportExcelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:1024', 'mimes:xlsx,xls'],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Не загружен файл',
            'file.file' => 'Не загружен файл',
            'file.mimes' => 'Файл должен быть Excel файлом',
            'file.max' => 'Размер файла не должен превышать 1 мегабайт',
        ];
    }
}
