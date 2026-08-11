<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreGearRequest extends FormRequest
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
            "title" => ["string", "max:150", "min:0", "required"],
            "description" => ["string", "nullable"],
            "type" => ["string", "max:50", "min:0", "required"],
            "price_per_day" => ["decimal", "required", "numeric", "min:0", "decimal:2"],
            "city" => ["string", "max:100", "min:0", "required"],
            "address" => ["string", "max:255", "min:0", "required"],
        ];
    }
}
