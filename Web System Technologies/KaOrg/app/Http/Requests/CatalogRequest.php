<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CatalogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can_manage_members;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $level = $this->routeIs('levels.*');
        $table = $level ? 'membership_levels' : 'membership_categories';
        $model = $this->route($level ? 'level' : 'category');
        $rules = ['name' => ['required', 'string', 'max:100', Rule::unique($table)->ignore($model)]];
        if ($level) {
            $rules += [
                'fee' => ['required', 'regex:/^[0-9]{1,9}(\.[0-9]{1,2})?$/', 'numeric', 'gt:0'],
                'validity_days' => ['required', 'integer', 'min:1', 'max:36500'],
                'benefits' => ['nullable', 'string', 'max:10000'],
            ];
        }

        return $rules;
    }
}
