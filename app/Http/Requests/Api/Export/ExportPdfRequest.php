<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Export;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExportPdfRequest extends FormRequest
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
            'key' => ['required', 'string', 'max:36'],
            'template_id' => ['required',
                Rule::exists('document_templates', 'id')->where('user_id', $this->user()->id),
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'key.required' => 'Укажите ключ',
            'key.max' => 'Размер ключа не должен превышать 36 символов',
            'template_id.required' => 'Укажите шаблон',
            'template_id.exists' => 'Шаблон не найден',
        ];
    }
}
