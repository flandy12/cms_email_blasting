<?php

namespace App\Http\Requests\EmailSchedule;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmailScheduleRequest extends FormRequest
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
            'template_id'  => ['required', 'exists:email_templates,id'],
            'schedule_at'  => ['required', 'date', 'after:now'],
            'recipients'   => ['required', 'array', 'min:1'],
            'recipients.*' => ['required', 'email'],
        ];
    }
}
