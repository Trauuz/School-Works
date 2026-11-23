<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $rules = [
            'phone' => ['nullable', 'string', 'max:30'],
            'address_line' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'region' => ['required', 'string', 'max:100'],
            'country_code' => ['required', 'string', 'regex:/^[A-Z]{2}$/'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'address_latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:address_longitude'],
            'address_longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:address_latitude'],
            'weather_location_id' => ['nullable', 'integer', 'min:1'],
        ];
        if ($this->routeIs('account.setup.store')) {
            $rules += ['role' => ['required', 'in:admin,member'], 'first_name' => ['required', 'string', 'max:100'], 'last_name' => ['required', 'string', 'max:100'], 'membership_category_id' => ['required_if:role,member', 'nullable', 'integer', 'exists:membership_categories,id'], 'membership_level_id' => ['required_if:role,member', 'nullable', 'integer', 'exists:membership_levels,id']];
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['country_code' => strtoupper(trim((string) $this->input('country_code')))]);
    }
}
