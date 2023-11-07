<?php

namespace App\Http\Requests\Api\Authentication;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordTokenRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'lowercase'],
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required'   => __('validation.required'),
            'email.string'     => __('validation.string'),
            'email.email'      => __('validation.email'),
            'email.lowercase'  => __('validation.lowercase'),
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'email' => __('validation.attributes.email'),
        ];
    }
}
