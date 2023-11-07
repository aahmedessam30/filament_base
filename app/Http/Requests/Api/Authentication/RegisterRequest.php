<?php

namespace App\Http\Requests\Api\Authentication;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name'     => ['required', 'string', 'min:3', 'max:255'],
            'email'    => ['required', 'string', 'email', 'lowercase', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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
            'name.required'      => __('validation.required'),
            'name.string'        => __('validation.string'),
            'name.min'           => __('validation.min.string'),
            'name.max'           => __('validation.max.string'),
            'email.required'     => __('validation.required'),
            'email.string'       => __('validation.string'),
            'email.email'        => __('validation.email'),
            'email.lowercase'    => __('validation.lowercase'),
            'email.unique'       => __('validation.unique'),
            'password.required'  => __('validation.required'),
            'password.string'    => __('validation.string'),
            'password.min'       => __('validation.min.string'),
            'password.confirmed' => __('validation.confirmed'),
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
            'name'     => __('validation.attributes.name'),
            'email'    => __('validation.attributes.email'),
            'password' => __('validation.attributes.password'),
        ];
    }
}
