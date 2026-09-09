<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name" => ["string", "required", "max:100"],
            "email" => ["required", "string", "max:255", "email:rfc", "unique:users,email"],
            "password" => ["required", "string", "min:8", "confirmed"],
            "phone" => ["required", "max:30", "string"],
        ];
    }
}
