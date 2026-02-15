<?php

namespace App\Http\Requests\Template;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBlastingTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ganti Gate/Policy jika perlu
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'subject' => ['required', 'string', 'max:150'],
            'wording' => ['required', 'string'],

            'button_type' => ['required', Rule::in(['none', 'button', 'wa'])],
            'button_text' => ['nullable', 'string', 'max:50'],

            'url_type' => ['nullable', Rule::in(['static', 'dynamic'])],
            'url' => ['nullable', 'url'],

            'params' => ['nullable', 'array'],
            'params.*.key' => ['required_with:params', 'string', 'max:50'],
            'params.*.value' => ['required_with:params', 'string', 'max:255'],
        ];
    }

    /**
     * Normalisasi data sebelum masuk controller
     */
    protected function prepareForValidation(): void
    {
        if ($this->button_type === 'none') {
            $this->merge([
                'button_text' => null,
                'url_type' => null,
                'url' => null,
                'params' => null,
            ]);
        }

        if ($this->url_type === 'static') {
            $this->merge([
                'params' => null,
            ]);
        }
    }
}
