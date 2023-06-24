<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TravelRequest extends FormRequest
{
    public function authorize(): bool
    {
        // NOTE:ログインチェックはルーティングで行っているのでここではtrueを返す
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'is_public' => 'boolean',
            'name' => 'required|unique:travels,name',
            'description' => 'required',
            'number_of_days' => 'required|integer'
        ];
    }
}
