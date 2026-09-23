<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            "price_per_day" => ["required", "numeric", "min:0", "decimal:2"],
            "city" => ["string", "max:100", "min:0", "required"],
            "address" => ["string", "max:255", "min:0", "required"],
            "category" => Rule::in(["hand_tool", "corded", "cordless", "machine"]),

            // akkus szerszámok (corded)
            "attributes.battery_capacity_mah" => [
                "prohibited_unless:category,cordless",
                "required_if:category,cordless",
                "integer",
                "min:0"
            ],

            // vezetékes szerszámok (corded)
            "attributes.power_w" => [
                "prohibited_unless:category,corded",
                "required_if:category,corded",
                "integer",
                "min:0",
            ],

            // mindkét szerszámtípusnál opcionális
            "attributes.modes" => [
                "prohibited_unless:category,cordless,corded",
                "array",
                "min:1",
            ],
            "attributes.modes.*" => ["string", Rule::in([
                "drill",
                "hammer_drill",
                "chiseling",
                "screwdriving",
                "cutting",
                "grinding",
                "sanding"
            ])],

            "attributes.load_capacity_kg" => [
                "prohibited_unless:category,machine",
                "required_if:category,machine",
                "numeric",
                "min:0",
            ],

            "attributes.fuel_type" => [
                "prohibited_unless:category,machine",
                "required_if:category,machine",
                "string",
                Rule::in(["benzin", "dizel", "elektromos"]),
            ],

            "attributes.fuel_tank_l" => [
                "prohibited_unless:category,machine",
                "required_if:category,machine",
                "numeric",
                "min:0",
            ],

            "attributes.horsepower" => [
                "prohibited_unless:category,machine",
                "required_if:category,machine",
                "numeric",
                "min:0",
            ],

        ];
    }
}
