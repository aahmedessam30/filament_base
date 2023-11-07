<?php

namespace App\Http\Requests\Api\V1\Chat;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequestChat extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->user()->can('create-chat');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
       return [
           'body' => ['required', 'string', 'max:255'],
       ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.required' => __('validation.required'),
            'type.string'   => __('validation.string'),
            'type.max'      => __('validation.max.string'),
            'body.required' => __('validation.required'),
            'body.string'   => __('validation.string'),
            'body.max'      => __('validation.max.string'),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'type' => __('attributes.type'),
            'body' => __('attributes.body'),
        ];
    }
}
