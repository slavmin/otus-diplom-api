<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\DocumentTemplate;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentTemplateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:1024', 'mimes:docx', 'mimetypes:application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Не указано название',
            'name.string' => 'Название должно быть строкой',
            'name.max' => 'Название не должно превышать 255 символов',
            'file.required' => 'Загрузите файл',
            'file.file' => 'Шаблон должен быть файлом',
            'file.mimes' => 'Шаблон должен быть Word файлом',
            'file.mimetypes' => 'Шаблон должен быть Word файлом',
            'file.max' => 'Размер файла не должен превышать 1 мегабайт',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var User $user */
            $user = $this->user();
            if ($user->documentTemplates()->count() >= 3) {
                $validator->errors()->add('limit', 'Вы не можете создать больше 3 шаблонов');
            }
        });
    }
}
