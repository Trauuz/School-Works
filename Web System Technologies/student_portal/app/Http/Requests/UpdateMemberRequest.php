<?php

namespace App\Http\Requests;

use App\EligibilityStatus;
use App\MemberCategory;
use App\MemberLevel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateMemberRequest extends FormRequest
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
            'member_number' => ['required', 'string', 'max:30', Rule::unique('members')->ignore($this->route('member'))],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('members')->ignore($this->route('member'))],
            'phone' => ['nullable', 'string', 'max:30'],
            'category' => ['required', Rule::enum(MemberCategory::class)],
            'level' => ['required', Rule::enum(MemberLevel::class)],
            'eligibility_status' => ['required', Rule::enum(EligibilityStatus::class)],
            'corporate_organization' => ['nullable', 'string', 'max:255', 'required_if:category,corporate'],
            'seat_count' => ['required', 'integer', 'min:1'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($this->input('category') === MemberCategory::Corporate->value && (int) $this->input('seat_count') < (int) config('memberships.minimum_corporate_seats')) {
                $validator->errors()->add('seat_count', 'Corporate memberships require at least 10 seats.');
            }

            if ($this->input('category') === MemberCategory::Regular->value && $this->input('eligibility_status') !== EligibilityStatus::Exempt->value) {
                $validator->errors()->add('eligibility_status', 'Regular memberships do not require eligibility verification.');
            }
        }];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(collect($this->only(['member_number', 'first_name', 'last_name', 'email', 'phone', 'corporate_organization']))
            ->map(fn (mixed $value): mixed => is_string($value) ? trim($value) : $value)
            ->all());
    }
}
